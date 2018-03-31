<?php

namespace Blixt\Storage\Drivers\Memory;

use Blixt\Persistence\Drivers\AbstractDriver;
use Blixt\Persistence\Drivers\Driver;
use Blixt\Storage\Entities\Entity;
use InvalidArgumentException;

class MemoryDriver extends AbstractDriver implements Driver
{
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
        $this->data = [];
        $this->keys = [];
    }

    /**
     * Tell if the storage represented by the engine exists.
     *
     * @return boolean
     */
    public function exists(): bool
    {
        foreach ($this->entities as $entity) {
            if (! isset($this->data[$this->getTableFromEntityClassName($entity)])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create the storage represented by the engine.
     *
     * @return bool
     */
    public function create(): bool
    {
        foreach ($this->entities as $entity) {
            $table = $this->getTableFromEntityClassName($entity);
            $this->data[$table] = [];
            $this->keys[$table] = 1;
        }

        return true;
    }

    /**
     * Create a new entry in the data for the given table and return the new key.
     *
     * @param string $table
     * @param array  $data
     *
     * @return array
     */
    public function insert(string $table, array $data): array
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
    public function find(string $table, int $key): Entity
    {
        $this->assertTableExists($table);

        return isset($this->data[$table][$key]) ? $this->data[$table][$key] : null;
    }

    /**
     * Get all of the entries in the dataset for the given table, where the value identified by the column in each item
     * is equal to the provided value.
     *
     * @param string $table
     * @param array  $conditions
     *
     * @return array
     */
    public function getWhere($table, array $conditions)
    {
        $this->assertTableExists($table);

        return array_filter($this->data[$table], function ($item) use ($conditions) {
            foreach ($conditions as $key => $value) {
                if ((is_array($value) && ! in_array($item[$key], $value)) || $item[$key] != $value) {
                    return false;
                }
            }

            return true;
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

        return $this->keys[$table]++;
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
}