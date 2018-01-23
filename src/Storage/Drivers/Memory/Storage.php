<?php

namespace Blixt\Storage\Drivers\Memory;

use Blixt\Storage\Drivers\Memory\Repositories\ColumnRepository;
use Blixt\Storage\Drivers\Memory\Repositories\DocumentRepository;
use Blixt\Storage\Drivers\Memory\Repositories\FieldRepository;
use Blixt\Storage\Drivers\Memory\Repositories\OccurrenceRepository;
use Blixt\Storage\Drivers\Memory\Repositories\PositionRepository;
use Blixt\Storage\Drivers\Memory\Repositories\SchemaRepository;
use Blixt\Storage\Drivers\Memory\Repositories\TermRepository;
use Blixt\Storage\Drivers\Memory\Repositories\WordRepository;
use Blixt\Storage\Storage as StorageInterface;
use InvalidArgumentException;

class Storage implements StorageInterface
{
    /**
     * @var array
     */
    protected static $tables = [
        'columns',
        'documents',
        'fields',
        'occurrences',
        'positions',
        'schemas',
        'terms',
        'words'
    ];

    /**
     * @var array
     */
    protected $repositories;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $keys;

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        $this->repositories = [];
        $this->data = [];
        $this->keys = [];
    }

    /**
     * Tell if the storage represented by the engine exists.
     *
     * @return boolean
     */
    public function exists()
    {
        foreach (self::$tables as $table) {
            if (! isset($this->data[$table])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a new entry in the data for the given table and return the new key.
     *
     * @param string $table
     * @param array  $data
     *
     * @return int
     */
    public function insert($table, array $data)
    {
        $this->assertTableExists($table);

        $this->data[$table][$key = $this->nextKey($table)] = $data;

        return $key;
    }

    /**
     * Update the data identified by the given key in the given table, with the data provided. The data provided is
     * merged into the existing data, which allows for full, or partial updates. True is returned upon success.
     *
     * @param string $table
     * @param int    $key
     * @param array  $data
     *
     * @return bool
     */
    public function update($table, $key, array $data)
    {
        $this->assertTableExists($table);

        $this->data[$table][$key] = array_merge($this->data[$table][$key], $data);

        return true;
    }

    /**
     * Find an entity by the given table and key. If no such entity could be found, null is returned instead.
     *
     * @param string $table
     * @param int    $key
     *
     * @return \Blixt\Storage\Entities\Entity|null
     */
    public function find($table, $key)
    {
        $this->assertTableExists($table);

        return isset($this->data[$table][$key]) ? $this->data[$table][$key] : null;
    }

    /**
     * Get all of the entries in the dataset for the given table, where the value identified by the column in each item
     * is equal to the provided value.
     *
     * @param string       $table
     * @param string|mixed $column
     * @param mixed        $value
     *
     * @return array
     */
    public function getWhere($table, $column, $value)
    {
        $this->assertTableExists($table);

        return array_filter($this->data[$table], function ($item) use ($column, $value) {
            return $item[$column] == $value;
        });
    }

    /**
     * Get all of the data for the given table.
     *
     * @param string $table
     *
     * @return array
     */
    public function all($table)
    {
        $this->assertTableExists($table);

        return $this->data[$table];
    }

    /**
     * Get the next key for the given table, incrementing the internal key value so that each subsequent call of nextKey
     * returns a number higher than the previous.
     *
     * @param string $table
     *
     * @return int
     */
    protected function nextKey($table)
    {
        $this->assertTableExists($table);

        return ++$this->keys[$table];
    }

    /**
     * Ensure that the given table exists in the data, throwing an InvalidArgumentException if not.
     *
     * @param string $table
     */
    protected function assertTableExists($table)
    {
        if (! isset($this->data[$table], $this->keys[$table])) {
            throw new InvalidArgumentException("The table '{$table}' does not exist.");
        }
    }

    /**
     * Create the storage represented by the engine.
     *
     * @return bool
     */
    public function create()
    {
        foreach (self::$tables as $table) {
            $this->data[$table] = [];
            $this->keys[$table] = 0;
        }

        return true;
    }

    /**
     * Destroy the storage represented by the engine.
     *
     * @return boolean
     */
    public function destroy()
    {
        $this->data = [];
        $this->keys = [];

        return true;
    }

    /**
     * Get (or create) the column repository.
     *
     * @return \Blixt\Storage\Repositories\ColumnRepository
     */
    public function columns()
    {
        return $this->loadRepository(ColumnRepository::class);
    }

    /**
     * Get (or create) the document repository.
     *
     * @return \Blixt\Storage\Repositories\DocumentRepository
     */
    public function documents()
    {
        return $this->loadRepository(DocumentRepository::class);
    }

    /**
     * Get (or create) the field repository.
     *
     * @return \Blixt\Storage\Repositories\FieldRepository
     */
    public function fields()
    {
        return $this->loadRepository(FieldRepository::class);
    }

    /**
     * Get (or create) the occurrence repository.
     *
     * @return \Blixt\Storage\Repositories\OccurrenceRepository
     */
    public function occurrences()
    {
        return $this->loadRepository(OccurrenceRepository::class);
    }

    /**
     * Get (or create) the position repository.
     *
     * @return \Blixt\Storage\Repositories\PositionRepository
     */
    public function positions()
    {
        return $this->loadRepository(PositionRepository::class);
    }

    /**
     * Get (or create) the schema repository.
     *
     * @return \Blixt\Storage\Repositories\SchemaRepository
     */
    public function schemas()
    {
        return $this->loadRepository(SchemaRepository::class);
    }

    /**
     * Get (or create) the term repository.
     *
     * @return \Blixt\Storage\Repositories\TermRepository
     */
    public function terms()
    {
        return $this->loadRepository(TermRepository::class);
    }

    /**
     * Get (or create) the word repository.
     *
     * @return \Blixt\Storage\Repositories\WordRepository
     */
    public function words()
    {
        return $this->loadRepository(WordRepository::class);
    }

    /**
     * Create and cache a repository with the given class name.
     *
     * @param string $class
     *
     * @return mixed
     */
    protected function loadRepository($class)
    {
        if (! isset($this->repositories[$class])) {
            $this->repositories[$class] = new $class($this);
        }

        return $this->repositories[$class];
    }
}