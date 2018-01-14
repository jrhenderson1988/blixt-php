<?php

namespace Blixt;

use Blixt\Index\Index;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\StorageFactory;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Tokenizer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class Blixt
{
    /**
     * @var \Blixt\Storage\StorageFactory
     */
    protected $entityManager;

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
     * @param array                              $connection
     * @param \Blixt\Stemming\Stemmer|null       $stemmer
     * @param \Blixt\Tokenization\Tokenizer|null $tokenizer
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function __construct(array $connection, Stemmer $stemmer = null, Tokenizer $tokenizer = null)
    {
        $this->stemmer = $stemmer instanceof Stemmer ? $stemmer : new EnglishStemmer();
        $this->tokenizer = $tokenizer instanceof Tokenizer ? $tokenizer : new DefaultTokenizer();

        $this->entityManager = EntityManager::create(
            $connection,
            Setup::createAnnotationMetadataConfiguration([__DIR__ . '/Storage/Entities'])
        );
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
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
}