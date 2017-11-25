<?php

namespace Blixt\Index;

use Blixt\Documents\Document as Indexable;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\UndefinedSchemaException;
use Blixt\Index\Schema\Schema;
use Blixt\Models\Column;
use Blixt\Stemming\StemmerContract as Stemmer;
use Blixt\Storage\StorageContract as Storage;
use Blixt\Tokenization\TokenizerContract as Tokenizer;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Index
{
    /**
     * @var \Blixt\Storage\StorageContract
     */
    protected $storage;

    /**
     * @var \Blixt\Stemming\StemmerContract
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\TokenizerContract
     */
    protected $tokenizer;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * Index constructor.
     *
     * @param \Blixt\Stemming\StemmerContract       $stemmer
     * @param \Blixt\Tokenization\TokenizerContract $tokenizer
     * @param \Blixt\Storage\StorageContract        $storage
     * @param \Blixt\Index\Schema\Schema|null       $schema
     *
     * @throws \Blixt\Exceptions\UndefinedSchemaException
     */
    public function __construct(Stemmer $stemmer, Tokenizer $tokenizer, Storage $storage, Schema $schema = null)
    {
        $this->storage = $storage;
        $this->stemmer = $stemmer;
        $this->tokenizer = $tokenizer;

        $this->createIndexIfNotExists($schema);

        $this->initialiseColumns();

        var_dump($this);
    }

    /**
     * Create the index if it does not already exist, initialising it with the provided schema.
     *
     * @param \Blixt\Index\Schema\Schema|null $schema
     *
     * @throws \Blixt\Exceptions\UndefinedSchemaException
     */
    protected function createIndexIfNotExists(Schema $schema = null)
    {
        if (!$this->storage->exists()) {
            if (!is_null($schema)) {
                $this->storage->transaction(function () use ($schema) {
                    $this->storage->create($schema);
                });
            } else {
                throw new UndefinedSchemaException(
                    "No schema provided to create index '{$this->storage->getName()}'."
                );
            }
        }
    }

    /**
     * Load the columns from the storage into the columns property. Also, key the columns by their names to make looking
     * them up easier.
     */
    protected function initialiseColumns()
    {
        $this->columns = $this->storage->transaction(function () {
            return $this->storage->getColumns()->keyBy(function (Column $column) {
                return $column->getName();
            });
        });
    }

    /**
     * Add a document, or a collection of documents to the index.
     *
     * @param \Illuminate\Support\Collection|\Blixt\Documents\Document|array $indexables
     *
     * @return bool
     */
    public function add($indexables)
    {
        $indexables = $this->makeDocumentCollection($indexables);

        $this->ensureDocumentsDoNotExist($indexables);

        $this->storage->transaction(function () use ($indexables) {
            $indexables->each(function (Indexable $indexable) {
                $document = $this->storage->createDocument($indexable->getKey());
            });
        });


    }

    /**
     * Convert the provided documents to a collection.
     *
     * @param \Illuminate\Support\Collection|\Blixt\Documents\Document|array $documents
     *
     * @return \Illuminate\Support\Collection
     */
    protected function makeDocumentCollection($documents)
    {
        if ($documents instanceof Collection) {
            return $documents;
        } elseif (is_array($documents)) {
            return new Collection($documents);
        } elseif ($documents instanceof Indexable) {
            return new Collection([$documents]);
        }

        throw new InvalidArgumentException("Expected a document, or a collection/array of documents.");
    }

    /**
     * Ensure that each of the provided documents are not already present in the index.
     *
     * @param \Illuminate\Support\Collection $documents
     */
    protected function ensureDocumentsDoNotExist(Collection $documents)
    {
        $documents->each(function (Indexable $indexable) {
            $document = $this->storage->transaction(function () use ($indexable) {
                $this->storage->findDocumentByKey($indexable->getKey());
            });

            if ($document) {
                throw new DocumentAlreadyExistsException(
                    "Document with key {$document->getKey()} already exists in {$this->getName()} index."
                );
            }
        });
    }

    public function update($key, Indexable $document)
    {
        if ($this->remove($key)) {
            $this->add($document);
        }
    }

    public function remove($key)
    {
        return true;
    }

    public function search()
    {

    }

    public function destroy()
    {
        if (!$this->storage->exists()) {
            return false;
        }

        return $this->storage->destroy();
    }

    /**
     * Get the name of the index represented by the storage.
     *
     * @return string
     */
    protected function getName()
    {
        return $this->storage->getName();
    }
}