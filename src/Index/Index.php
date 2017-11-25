<?php

namespace Blixt\Index;

use Blixt\Documents\Document as IndexableDocument;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\UndefinedSchemaException;
use Blixt\Index\Schema\Schema;
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
     * Load the columns from the storage into the columns property.
     */
    protected function initialiseColumns()
    {
        $this->columns = $this->storage->transaction(function () {
            return $this->storage->getColumns();
        });
    }

    /**
     * @param \Illuminate\Support\Collection|\Blixt\Documents\Document|array $documents
     *
     * @return bool
     */
    public function add($documents)
    {
        if (is_array($documents)) {
            return $this->add(new Collection($documents));
        } elseif ($documents instanceof IndexableDocument) {
            return $this->add(new Collection([$documents]));
        }

        if (!$documents instanceof Collection) {
            throw new InvalidArgumentException(
                "Expected a document, or a collection/array of documents."
            );
        }

        var_dump($documents);

        $documents->each(function (IndexableDocument $document) {
            if ($this->storage->findDocumentByKey($document->getKey())) {
                throw new DocumentAlreadyExistsException(
                    "Document with key {$document->getKey()} already exists in {$this->getName()} index."
                );
            }

            // TODO - Index the document.
        });
    }

    public function update($key, IndexableDocument $document)
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