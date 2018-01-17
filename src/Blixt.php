<?php

namespace Blixt;

use Blixt\Index\Index;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\StorageFactory;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Tokenizer;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Illuminate\Support\Collection;

// TODO - Blixt class now represents the parent index, the index class now represents a sub-index based around a schema

class Blixt
{
    /**
     * @var \Doctrine\ORM\EntityManager
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

        // TODO - This has a performance impact. Extract it somewhere to be manually run once, perhaps a static method.
        if (!$this->exists()) {
            $this->create();
        }

        var_dump($this->getEntityManager()->getRepository(\Blixt\Storage\Entities\Schema::class)->findAll());
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
     * @param \Blixt\Index\Schema\Schema|callable|null  $schema
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
        $existingTables = new Collection($this->getEntityManager()->getConnection()->getSchemaManager()->listTables());
        $existingTableNames = $existingTables->map(function (Table $table) {
            return $table->getName();
        });

        $entityMetaData = new Collection($this->getEntityManager()->getMetadataFactory()->getAllMetadata());
        $entityTableNames = $entityMetaData->map(function (ClassMetadata $entityMetaDatum) {
            return $entityMetaDatum->getTableName();
        });

        return $entityTableNames->diff($existingTableNames)->isEmpty();
    }

    /**
     * Create the schema defined by the entities.
     *
     * @return bool
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function create()
    {
        $schemaTool = new SchemaTool($this->getEntityManager());

        $schemaTool->createSchema(
            $this->getEntityManager()->getMetadataFactory()->getAllMetadata()
        );

        return true;
    }
}