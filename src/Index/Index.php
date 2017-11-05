<?php

namespace Blixt\Index;

use Blixt\Documents\Document as IndexableDocument;
use Blixt\Exceptions\StorageException;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\StemmerInterface as Stemmer;
use Blixt\Storage\FactoryInterface as StorageFactory;
use Blixt\Tokenization\TokenizerInterface as Tokenizer;
use Closure;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Index
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Blixt\Storage\EngineInterface
     */
    protected $storage;

    /**
     * @var \Blixt\Stemming\StemmerInterface
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\TokenizerInterface
     */
    protected $tokenizer;

    /**
     * Index constructor.
     *
     * @param string                                 $name
     * @param \Blixt\Storage\FactoryInterface        $connector
     * @param \Blixt\Stemming\StemmerInterface       $stemmer
     * @param \Blixt\Tokenization\TokenizerInterface $tokenizer
     *
     * @throws \Exception
     */
    public function __construct($name, StorageFactory $connector, Stemmer $stemmer, Tokenizer $tokenizer)
    {
        $this->name = $name;
        $this->storage = $connector->create($name);
        $this->stemmer = $stemmer;
        $this->tokenizer = $tokenizer;
    }

    /**
     * Tel if this index exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->storage->exists();
    }

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
                throw new \Exception('Document already exists (Throw a different exception)...');
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
     * @throws \Blixt\Exceptions\StorageException
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

            throw new StorageException(
                $ex->getMessage(), $ex->getCode(), $ex
            );
        }
    }
}