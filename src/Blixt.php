<?php

namespace Blixt;

use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Exceptions\InvalidBlueprintException;
use Blixt\Exceptions\StorageException;
use Blixt\Index\Index;
use Blixt\Index\Schema\Blueprint;
use Blixt\Index\Schema\Definition;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Tokenizer;

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
    public $schemas;

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

        $this->reloadSchemas();
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
    protected function reloadSchemas()
    {
        $columns = $this->getStorage()->columns()->all();

        // Note: The Schema::setColumns method filters out columns that do not belong to it.
        $this->schemas = $this->getStorage()->schemas()->all()->map(function (Schema $schema) use ($columns) {
            $schema->setColumns($columns);

            return $schema;
        });
    }

    /**
     * Open an index for the given schema.
     *
     * @param string|int|mixed $schema
     *
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\IndexDoesNotExistException
     */
    public function open($schema)
    {
        $schema = $this->findSchema($schema);

        if (! $schema) {
            throw new IndexDoesNotExistException(
                'The requested schema does not exist.'
            );
        }

        return $this->createIndexForSchema($schema);
    }

    /**
     * Create an Index object using the Schema created by the Blueprint.
     *
     * @param \Blixt\Index\Schema\Blueprint|string|mixed $blueprint
     * @param \Closure|callable|null                     $closure
     *
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\StorageException
     * @throws \Blixt\Exceptions\IndexDoesNotExistException
     */
    public function create($blueprint, $closure = null)
    {
        $blueprint = $this->buildBlueprint($blueprint, $closure);
        if ($schema = $this->findSchema($blueprint->getName())) {
            throw new IndexAlreadyExistsException(
                "An index with name '{$blueprint->getName()}' already exists."
            );
        }

        $schema = $this->createSchemaFromBlueprint($blueprint);

        $this->reloadSchemas();

        return $this->open($schema->getName());
    }

    /**
     * Find the schema identified by the ID or name provided.
     *
     * @param string|int|mixed $identifier
     *
     * @return \Blixt\Storage\Entities\Schema|null
     */
    protected function findSchema($identifier)
    {
        if (is_int($identifier) || ctype_digit($identifier)) {
            $schema = $this->schemas->first(function (Schema $schema) use ($identifier) {
                return $schema->getId() == $identifier;
            });

            if ($schema) {
                return $schema;
            }
        }

        // find by name
        return $this->schemas->first(function (Schema $schema) use ($identifier) {
            return $schema->getName() == $identifier;
        });
    }

    /**
     * Build a Blueprint object with the given parameters. If the blueprint parameter provided is not an instance of
     * Blueprint, create a new one assuming it is a name. If a callable is provided, call it and pass in the (new or
     * provided) Blueprint object, which allows the developer to dynamically specify a column definition.
     *
     * @param \Blixt\Index\Schema\Blueprint|string|mixed $blueprint
     * @param \Closure|callable|null                     $callable
     *
     * @return \Blixt\Index\Schema\Blueprint
     */
    protected function buildBlueprint($blueprint, $callable)
    {
        if (! $blueprint instanceof Blueprint) {
            $blueprint = new Blueprint($blueprint);
        }

        if (is_callable($callable)) {
            call_user_func($callable, $blueprint);
        }

        return $blueprint;
    }

    /**
     * Create a Schema for the given Blueprint.
     *
     * @param \Blixt\Index\Schema\Blueprint $blueprint
     *
     * @return \Blixt\Storage\Entities\Schema
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function createSchemaFromBlueprint(Blueprint $blueprint)
    {
        if ($blueprint->getDefinitions()->count() < 1) {
            throw new InvalidBlueprintException(
                "At least one column must be defined to create a new index."
            );
        }

        $schema = $this->getStorage()->schemas()->create($blueprint->getName());
        if (! $schema) {
            throw new StorageException(
                "Could not create schema '{$blueprint->getName()}'."
            );
        }

        $blueprint->getDefinitions()->each(function (Definition $column) use ($schema) {
            $this->getStorage()->columns()->create(
                $schema->getId(),
                $column->getName(),
                $column->isIndexed(),
                $column->isStored()
            );
        });

        return $schema;
    }

    /**
     * Create an Index object for the given schema.
     *
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Index\Index
     */
    protected function createIndexForSchema(Schema $schema)
    {
        return new Index($this, $schema);
    }
}