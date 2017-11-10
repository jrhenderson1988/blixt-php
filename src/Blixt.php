<?php

namespace Blixt;

use Blixt\Index\Index;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Stemming\StemmerContract as Stemmer;
use Blixt\Storage\StorageFactoryContract as StorageFactory;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\TokenizerContract as Tokenizer;

class Blixt
{
    /**
     * @var \Blixt\Storage\StorageFactoryContract
     */
    protected $storageFactory;

    /**
     * @var \Blixt\Stemming\StemmerContract
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\TokenizerContract
     */
    protected $tokenizer;

    /**
     * Blixt constructor.
     *
     * @param \Blixt\Storage\StorageFactoryContract      $storageFactory
     * @param \Blixt\Stemming\StemmerContract|null       $stemmer
     * @param \Blixt\Tokenization\TokenizerContract|null $tokenizer
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
     * @param \Blixt\Storage\StorageFactoryContract $storage
     */
    public function setStorageFactory(StorageFactory $storage)
    {
        $this->storageFactory = $storage;
    }

    /**
     * Get the storage connector.
     *
     * @return \Blixt\Storage\StorageFactoryContract
     */
    public function getStorageFactory()
    {
        return $this->storageFactory;
    }

    /**
     * Set the stemmer.
     *
     * @param \Blixt\Stemming\StemmerContract $stemmer
     */
    public function setStemmer(Stemmer $stemmer)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * Get the stemmer.
     *
     * @return \Blixt\Stemming\StemmerContract
     */
    public function getStemmer()
    {
        return $this->stemmer;
    }

    /**
     * Set the tokenizer.
     *
     * @param \Blixt\Tokenization\TokenizerContract $tokenizer
     */
    public function setTokenizer(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * Get the tokenizer.
     *
     * @return \Blixt\Tokenization\TokenizerContract
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

        if (is_callable($callable = $schema)) {
            $schema = new Schema();

            call_user_func($callable, $schema);
        }

        return new Index(
            $this->getStemmer(),
            $this->getTokenizer(),
            $storageFactory->create($name),
            $schema
        );

//        $index = $this->makeIndex($name);
//
//        if (!$index->exists()) {
//            if ($schema instanceof Schema) {
//                $index->create($schema);
//            } elseif (is_callable($callable = $schema)) {
//                $schema = new Schema();
//                $callable($schema);
//
//                $index->create($schema);
//            } else {
//                throw new IndexDoesNotExistException(
//                    "The index '{$name}' does not exist."
//                );
//            }
//        }
//
//        return $index;
    }

//    /**
//     * Destroy an existing index.
//     *
//     * @param string $name
//     *
//     * @return bool
//     * @throws \Blixt\Exceptions\IndexDoesNotExistException
//     */
//    public function destroy($name)
//    {
//        $index = $this->makeIndex($name);
//
//        if (!$index->exists()) {
//            throw new IndexDoesNotExistException(
//                "The index '{$name}' does not exist."
//            );
//        }
//
//        return $index->destroy();
//    }

//    /**
//     * Create an Index object with the given name.
//     *
//     * @param string $name
//     *
//     * @return \Blixt\Index\Index
//     */
//    protected function makeIndex($name)
//    {
//        return new Index(
//            $name, $this->getStorageFactory(), $this->getStemmer(), $this->getTokenizer()
//        );
//    }
}