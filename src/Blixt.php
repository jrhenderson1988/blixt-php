<?php

namespace Blixt;

use Blixt\Stemming\StemmerInterface as Stemmer;
use Blixt\Storage\BuilderInterface as StorageBuilder;
use Blixt\Tokenization\TokenizerInterface as Tokenizer;

class Blixt
{
    /**
     * @var \Blixt\Storage\BuilderInterface
     */
    protected $storageBuilder;

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
     * @param \Blixt\Storage\BuilderInterface        $storage
     * @param \Blixt\Stemming\StemmerInterface       $stemmer
     * @param \Blixt\Tokenization\TokenizerInterface $tokenizer
     */
    public function __construct(StorageBuilder $storage, Stemmer $stemmer = null, Tokenizer $tokenizer = null)
    {
        $this->setStorageBuilder($storage);
        $this->setStemmer($stemmer);
        $this->setTokenizer($tokenizer);
    }

    /**
     * Set the storage engine.
     *
     * @param \Blixt\Storage\BuilderInterface $storage
     */
    public function setStorageBuilder(StorageBuilder $storage)
    {
        $this->storageBuilder = $storage;
    }

    /**
     * Set the stemmer.
     *
     * @param \Blixt\Stemming\StemmerInterface $stemmer
     */
    public function setStemmer(Stemmer $stemmer)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * Set the tokenizer.
     *
     * @param \Blixt\Tokenization\TokenizerInterface $tokenizer
     */
    public function setTokenizer(Tokenizer $tokenizer)
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
        return new Index($name, $this->storage);
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