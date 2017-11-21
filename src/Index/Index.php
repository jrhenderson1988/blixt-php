<?php

namespace Blixt\Index;

use Blixt\Documents\Document as IndexableDocument;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\UndefinedSchemaException;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\StemmerContract as Stemmer;
use Blixt\Storage\StorageContract as Storage;
use Blixt\Tokenization\TokenizerContract as Tokenizer;
use Closure;
use Exception;
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

        if (!$this->storage->exists()) {
            if (!is_null($schema)) {
                $this->transaction(function () use ($schema) {
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

    public function destroy()
    {
        if (!$this->storage->exists()) {
            return false;
        }

        return $this->storage->destroy();
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