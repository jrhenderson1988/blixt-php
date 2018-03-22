<?php

namespace Blixt;

use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Exceptions\InvalidBlueprintException;
use Blixt\Exceptions\StorageException;
use Blixt\Index\Index;
use Blixt\Index\Blueprint\Blueprint;
use Blixt\Index\Blueprint\Definition;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;
use Closure;

class Blixt
{
    /**
     * @var \Blixt\Storage\Storage
     */
    protected $storage;

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
     * @param \Blixt\Storage\Storage        $storage
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function __construct(Storage $storage, Tokenizer $tokenizer)
    {
        $this->storage = $storage;
        $this->tokenizer = $tokenizer;
    }

    /**
     * Install Blixt into the storage engine. This effectively creates the underlying storage schema if it does not
     * already exist.
     *
     * @return bool
     */
    public function install(): bool
    {
        if (! $this->getStorage()->exists()) {
            return $this->getStorage()->create();
        }

        return true;
    }

    /**
     * @return \Blixt\Storage\Storage
     */
    public function getStorage(): Storage
    {
        return $this->storage;
    }

    /**
     * Get the tokenizer.
     *
     * @return \Blixt\Tokenization\Tokenizer
     */
    public function getTokenizer(): Tokenizer
    {
        return $this->tokenizer;
    }

    /**
     * Open an Index object for an existing schema with the given name. If the schema does not exist, but a closure has
     * been provided, a Blueprint object is created and used to create a schema with which we can open an Index object.
     *
     * @param string   $name
     * @param \Closure $closure
     *
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\IndexDoesNotExistException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function open(string $name, Closure $closure = null): Index
    {
        if ($schema = $this->findSchemaByName($name)) {
            return $this->createIndexForSchema($schema);
        }

        if ($closure !== null) {
            $schema = $this->createSchemaFromBlueprint(
                $this->buildBlueprint($name, $closure)
            );

            return $this->createIndexForSchema($schema);
        }

        throw new IndexDoesNotExistException("The requested schema does not exist.");
    }

    /**
     * Given a blueprint, create a schema and create an Index object that encapsulates it.
     *
     * @param \Blixt\Index\Blueprint\Blueprint|string|mixed $blueprint
     *
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function create(Blueprint $blueprint): Index
    {
        if ($this->findSchemaByName($blueprint->getName())) {
            throw new IndexAlreadyExistsException("An index with name '{$blueprint->getName()}' already exists.");
        }

        return $this->createIndexForSchema(
            $this->createSchemaFromBlueprint($blueprint)
        );
    }

    /**
     * Find the schema by the name provided.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\Entities\Schema|null
     */
    protected function findSchemaByName(string $name): ?Schema
    {
        return $this->getStorage()->schemas()->findByName($name);
    }

    /**
     * Build a Blueprint object with the given name and closure. A new Blueprint object is created using the name and
     * passed into the closure so that the developer may define fields for the schema. The resulting blueprint is
     * returned.
     *
     * @param string   $name
     * @param \Closure $closure
     *
     * @return \Blixt\Index\Blueprint\Blueprint
     */
    protected function buildBlueprint(string $name, Closure $closure): Blueprint
    {
        $blueprint = new Blueprint($name);

        $closure($blueprint);

        return $blueprint;
    }

    /**
     * Create a Schema for the given Blueprint.
     *
     * @param \Blixt\Index\Blueprint\Blueprint $blueprint
     *
     * @return \Blixt\Storage\Entities\Schema
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function createSchemaFromBlueprint(Blueprint $blueprint)
    {
        if ($blueprint->getDefinitions()->isEmpty()) {
            throw new InvalidBlueprintException("At least one column must be defined to create a new index.");
        }

        $schema = $this->getStorage()->schemas()->save(
            (new Schema())->name($blueprint->getName())
        );

        if (! $schema) {
            throw new StorageException("Could not create schema '{$blueprint->getName()}'.");
        }

        $blueprint->getDefinitions()->each(function (Definition $column) use ($schema) {
            $this->getStorage()->columns()->save(
                (new Column())
                    ->schemaId($schema->getId())
                    ->name($column->getName())
                    ->indexed($column->isIndexed())
                    ->stored($column->isStored())
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
        return new Index($schema, $this->storage, $this->tokenizer);
    }
}