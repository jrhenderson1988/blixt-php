<?php

namespace Blixt;

use Blixt\Index\Index;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Stemming\Stemmer as Stemmer;
use Blixt\Storage\StorageFactory as StorageFactory;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Tokenizer as Tokenizer;

class Blixt
{
    /**
     * @var \Blixt\Storage\StorageFactory
     */
    protected $storageFactory;

    /**
     * @var \Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * Blixt constructor.
     *
     * @param \Blixt\Storage\StorageFactory      $storageFactory
     * @param \Blixt\Stemming\Stemmer|null       $stemmer
     * @param \Blixt\Tokenization\Tokenizer|null $tokenizer
     */
    public function __construct(StorageFactory $storageFactory, Stemmer $stemmer = null, Tokenizer $tokenizer = null)
    {
        $this->setStorageFactory($storageFactory);
        $this->setStemmer($stemmer ?: new EnglishStemmer());
        $this->setTokenizer($tokenizer ?: new DefaultTokenizer());
    }

    /**
     * Set the storage factory responsible for creating the storage driver.
     *
     * @param \Blixt\Storage\StorageFactory $storage
     */
    public function setStorageFactory(StorageFactory $storage)
    {
        $this->storageFactory = $storage;
    }

    /**
     * Get the storage connector.
     *
     * @return \Blixt\Storage\StorageFactory
     */
    public function getStorageFactory()
    {
        return $this->storageFactory;
    }

    /**
     * Set the stemmer.
     *
     * @param \Blixt\Stemming\Stemmer $stemmer
     */
    public function setStemmer(Stemmer $stemmer)
    {
        $this->stemmer = $stemmer;
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
     * Set the tokenizer.
     *
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function setTokenizer(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
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
     * Open an existing index with the given name. An optional schema may be provided as a callable or Schema object
     * that may be used to create a non-existent index.
     *
     * @param string                                    $name
     * @param \Blixt\Index\Schema\Schema|callable|null  $schema
     *
     * @return \Blixt\Index\Index
     */
    public function open($name, $schema = null)
    {
        $storageFactory = $this->getStorageFactory();

        if (!is_null($schema) && is_callable($callable = $schema)) {
            $schema = new Schema();

            call_user_func($callable, $schema);
        }

        return new Index(
            $this->getStemmer(),
            $this->getTokenizer(),
            $storageFactory->create($name),
            $schema
        );
    }
}