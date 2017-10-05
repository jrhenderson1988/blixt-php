<?php

namespace Blixt;

use Blixt\Stemming\StemmerInterface as Stemmer;
use Blixt\Storage\Connectors\ConnectorInterface as Connector;
use Blixt\Tokenization\TokenizerInterface as Tokenizer;

class Blixt
{
    /**
     * @var \Blixt\Storage\Connectors\ConnectorInterface
     */
    protected $storageConnector;

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
     * @param \Blixt\Storage\Connectors\ConnectorInterface $storage
     * @param \Blixt\Stemming\StemmerInterface             $stemmer
     * @param \Blixt\Tokenization\TokenizerInterface       $tokenizer
     */
    public function __construct(Connector $storage, Stemmer $stemmer = null, Tokenizer $tokenizer = null)
    {
        $this->setStorageConnector($storage);
        $this->setStemmer($stemmer);
        $this->setTokenizer($tokenizer);
    }

    /**
     * Set the storage factory responsible for creating the storage driver.
     *
     * @param \Blixt\Storage\Connectors\ConnectorInterface $storage
     */
    public function setStorageConnector(Connector $storage)
    {
        $this->storageConnector = $storage;
    }

    /**
     * Get the storage connector.
     *
     * @return \Blixt\Storage\Connectors\ConnectorInterface
     */
    public function getStorageConnector()
    {
        return $this->storageConnector;
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
     * Get the stemmer.
     *
     * @return \Blixt\Stemming\StemmerInterface
     */
    public function getStemmer()
    {
        return $this->stemmer;
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
     * Get the tokenizer.
     *
     * @return \Blixt\Tokenization\TokenizerInterface
     */
    public function getTokenizer()
    {
        return $this->tokenizer;
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
        return new Index(
            $name, $this->getStorageConnector()
        );
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