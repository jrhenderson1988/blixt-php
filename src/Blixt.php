<?php

namespace Blixt;

use Blixt\Stemming\EnglishStemmer;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;

// TODO - Blixt class now represents the parent index, the index class now represents a sub-index based around a schema

class Blixt
{
    /**
     * @var \Blixt\Storage\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $schemas;

    /**
     * Blixt constructor.
     *
     * @param \Blixt\Storage\Storage             $storage
     * @param \Blixt\Stemming\Stemmer|null       $stemmer
     * @param \Blixt\Tokenization\Tokenizer|null $tokenizer
     */
    public function __construct(Storage $storage, Stemmer $stemmer = null, Tokenizer $tokenizer = null)
    {
        $this->storage = $storage;
        $this->stemmer = $stemmer instanceof Stemmer ? $stemmer : new EnglishStemmer();
        $this->tokenizer = $tokenizer instanceof Tokenizer ? $tokenizer : new DefaultTokenizer();

        $this->loadSchemas();
    }

    /**
     * Install Blixt into the storage engine. This effectively creates the underlying storage schema if it does not
     * already exist.
     *
     * @param \Blixt\Storage\Storage $storage
     *
     * @return bool
     */
    public static function install(Storage $storage)
    {
        if (! $storage->exists()) {
            return $storage->create();
        }

        return true;
    }

    /**
     * @return \Blixt\Storage\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Get the stemmer.
     *
     * @return \Blixt\Stemming\Stemmer
     */
    public function getStemmer()
    {
        return $this->stemmer;
    }

    /**
     * Get the tokenizer.
     *
     * @return \Blixt\Tokenization\Tokenizer
     */
    public function getTokenizer()
    {
        return $this->tokenizer;
    }

    /**
     * Load all of the schemas from the storage, with their associated columns.
     */
    protected function loadSchemas()
    {
        $columns = $this->getStorage()->columns()->all();

        // Note: The Schema::setColumns method filters out columns that do not belong to it.
        $this->schemas = $this->getStorage()->schemas()->all()->map(function (Schema $schema) use ($columns) {
            $schema->setColumns($columns);
        });
    }

    /**
     * Open an existing index with the given name. An optional schema may be provided as a callable or Schema object
     * that may be used to create a non-existent index.
     *
     * @param \Blixt\Index\Schema\Blueprint|callable|null $schema
     *
     * @return \Blixt\Index\Index
     */
    public function open($schema = null)
    {
//        $storageFactory = $this->getStorageFactory();
//
//        if (!is_null($schema) && is_callable($callable = $schema)) {
//            $schema = new Schema();
//
//            call_user_func($callable, $schema);
//        }
//
//        return new Index(
//            $this->getStemmer(),
//            $this->getTokenizer(),
//            $storageFactory->create($name),
//            $schema
//        );
    }

    /**
     * Tell if the index already exists, that is, all of the tables specified by the entities exist in the database. If
     * any table is missing from the database, false is returned.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->getStorage()->exists();
    }

    /**
     * Create the schema defined by the entities.
     *
     * @return bool
     */
    public function create()
    {
        return $this->getStorage()->create();
    }
}