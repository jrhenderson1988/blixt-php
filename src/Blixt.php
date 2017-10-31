<?php

namespace Blixt;

use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Index\Index;
use Blixt\Index\Schema;
use Blixt\Stemming\StemmerInterface as Stemmer;
use Blixt\Storage\FactoryInterface as StorageFactory;
use Blixt\Tokenization\TokenizerInterface as Tokenizer;

class Blixt
{
    /**
     * @var \Blixt\Storage\FactoryInterface
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
     * @param \Blixt\Storage\FactoryInterface             $storageFactory
     * @param \Blixt\Stemming\StemmerInterface|null       $stemmer
     * @param \Blixt\Tokenization\TokenizerInterface|null $tokenizer
     */
    public function __construct(StorageFactory $storageFactory, Stemmer $stemmer = null, Tokenizer $tokenizer = null)
    {
        $this->setStorageFactory($storageFactory);
        $this->setStemmer($stemmer);
        $this->setTokenizer($tokenizer);
    }

    /**
     * Set the storage factory responsible for creating the storage driver.
     *
     * @param \Blixt\Storage\FactoryInterface $storage
     */
    public function setStorageFactory(StorageFactory $storage)
    {
        $this->storageFactory = $storage;
    }

    /**
     * Get the storage connector.
     *
     * @return \Blixt\Storage\FactoryInterface
     */
    public function getStorageFactory()
    {
        return $this->storageFactory;
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
     * Open an existing index. If the provided index does not exist, but a column definition is provided, the index is
     * created using that definition and then returned. If the index does not exist, and no column definition is
     * provided an exception is thrown.
     *
     * @param string $name
     * @param array  $schema
     *
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\IndexDoesNotExistException
     */
    public function open($name, $schema = null)
    {
        $index = $this->makeIndex($name);

        if (!$index->exists()) {
            if ($schema instanceof Schema) {
                $index->create($schema);
            } elseif (is_callable($schema)) {
                $schemaObject = new Schema();
                $schema($schemaObject);

                $index->create($schema);
            } else {
                throw new IndexDoesNotExistException(
                    "The index '{$name}' does not exist."
                );
            }
        }

        return $index;
    }

    /**
     * Destroy an existing index.
     *
     * @param string $name
     *
     * @return bool
     * @throws \Blixt\Exceptions\IndexDoesNotExistException
     */
    public function destroy($name)
    {
        $index = $this->makeIndex($name);

        if (!$index->exists()) {
            throw new IndexDoesNotExistException(
                "The index '{$name}' does not exist."
            );
        }

        return $index->destroy();
    }

    /**
     * Create an Index object with the given name.
     *
     * @param string $name
     *
     * @return \Blixt\Index\Index
     */
    protected function makeIndex($name)
    {
        return new Index(
            $name, $this->getStorageFactory(), $this->getStemmer(), $this->getTokenizer()
        );
    }
}