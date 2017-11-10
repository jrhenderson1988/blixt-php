<?php

namespace Blixt\Index;

use Blixt\Documents\Document as IndexableDocument;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\StemmerContract as Stemmer;
use Blixt\Storage\StorageEngineContract as Storage;
use Blixt\Tokenization\TokenizerContract as Tokenizer;
use Closure;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Index
{
    /**
     * @var \Blixt\Storage\StorageEngineContract
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
     * Index constructor.
     *
     * @param \Blixt\Stemming\StemmerContract       $stemmer
     * @param \Blixt\Tokenization\TokenizerContract $tokenizer
     * @param \Blixt\Storage\StorageEngineContract  $storage
     * @param \Blixt\Index\Schema\Schema|null       $schema
     */
    public function __construct(Stemmer $stemmer, Tokenizer $tokenizer, Storage $storage, Schema $schema = null)
    {
        $this->storage = $storage;
        $this->stemmer = $stemmer;
        $this->tokenizer = $tokenizer;

        if (!$this->storage->exists()) {
            if (!is_null($schema)) {
                $this->storage->create($schema);
            } else {
                // Throw exception
            }
        }
    }

//    /**
//     * Tel if this index exists.
//     *
//     * @return bool
//     */
//    public function exists()
//    {
//        return $this->storage->exists();
//    }

    /**
     * Create this index with the given column definition.
     *
     * @param \Blixt\Index\Schema\Schema $schema
     *
     * @return bool
     */
    public function create(Schema $schema)
    {
        return $this->transaction(function () use ($schema) {
            return $this->storage->create($schema);
        });
    }

    /**
     * Destroy the index.
     *
     * @return bool
     */
    public function destroy()
    {
        if ($this->exists()) {
            return $this->storage->destroy();
        }

        return false;
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

        $documents->each(function (IndexableDocument $document) {
            if ($this->storage->findDocumentByKey($document->getKey())) {
                throw new DocumentAlreadyExistsException(
                    "Document with key {$document->getKey()} already exists in {$this->name} index."
                );
            }

            // TODO - Index the document.
        });
    }

    public function update()
    {

    }

    public function search()
    {

    }

    /**
     * Execute the provided closure in a transaction. The return value of the closure is returned from this method. If
     * any exceptions are thrown within the closure, the transaction is rolled back and a StorageException is thrown
     * with the caught exception as the previous.
     *
     * @param \Closure $callback
     *
     * @return mixed
     * @throws \Exception
     */
    protected function transaction(Closure $callback)
    {
        $this->storage->beginTransaction();

        try {
            $response = $callback();

            $this->storage->commitTransaction();

            return $response;
        } catch (Exception $ex) {
            $this->storage->rollBackTransaction();

            throw $ex;
        }
    }
}