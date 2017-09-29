<?php

namespace Blixt;

use Blixt\Stemming\StemmerInterface;
use Blixt\Storage\StorageFactoryInterface;
use Blixt\Storage\StorageInterface;
use Blixt\Tokenization\TokenizerInterface;

class Blixt
{
    /**
     * @var \Blixt\Storage\StorageFactoryInterface
     */
    protected $storageFactory;

    /**
     * @var \Blixt\Stemming\StemmerInterface
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\TokenizerInterface
     */
    protected $tokenizer;

    /**
     * Blixt constructor.
     *
     * @param \Blixt\Storage\StorageFactoryInterface $storageFactory
     * @param \Blixt\Stemming\StemmerInterface       $stemmer
     * @param \Blixt\Tokenization\TokenizerInterface $tokenizer
     */
    public function __construct(StorageFactoryInterface $storageFactory, StemmerInterface $stemmer, TokenizerInterface $tokenizer)
    {
        $this->setStorageFactory($storageFactory);
        $this->setStemmer($stemmer);
        $this->setTokenizer($tokenizer);
    }

    /**
     * Set the storage engine.
     *
     * @param \Blixt\Storage\StorageInterface $storage
     */
    public function setStorageFactory(StorageInterface $storage)
    {
        $this->storageFactory = $storage;
    }

    /**
     * Set the stemmer.
     *
     * @param \Blixt\Stemming\StemmerInterface $stemmer
     */
    public function setStemmer(StemmerInterface $stemmer)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * Set the tokenizer.
     *
     * @param \Blixt\Tokenization\TokenizerInterface $tokenizer
     */
    public function setTokenizer(TokenizerInterface $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * Open an existing index (or create one if it does not exist).
     *
     * @param string $name
     *
     * @return \Blixt\Index
     */
    public function open($name)
    {
        return new Index($name, $this->storageFactory);
    }

    /**
     * Destroy an existing index.
     *
     * @param string $name
     */
    public function destroy($name)
    {

    }
}