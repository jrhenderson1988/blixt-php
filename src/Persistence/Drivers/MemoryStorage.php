<?php

namespace Blixt\Persistence\Drivers;

use Blixt\Exceptions\StorageException;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\ColumnRepository;
use Blixt\Persistence\Repositories\DocumentRepository;
use Blixt\Persistence\Repositories\FieldRepository;
use Blixt\Persistence\Repositories\OccurrenceRepository;
use Blixt\Persistence\Repositories\PositionRepository;
use Blixt\Persistence\Repositories\SchemaRepository;
use Blixt\Persistence\Repositories\TermRepository;
use Blixt\Persistence\Repositories\WordRepository;

class MemoryStorage extends AbstractStorage implements Storage
{
    /**
     * The tables that this driver must maintain.
     *
     * @var array
     */
    protected static $tables = [
        SchemaRepository::TABLE,
        ColumnRepository::TABLE,
        WordRepository::TABLE,
        TermRepository::TABLE,
        DocumentRepository::TABLE,
        FieldRepository::TABLE,
        OccurrenceRepository::TABLE,
        PositionRepository::TABLE
    ];

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
        foreach (static::$tables as $table) {
            if (! isset($this->data[$table])) {
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
    public function install(): bool
    {
        foreach (static::$tables as $table) {
            $this->data[$table] = [];
            $this->keys[$table] = 1;
        }

        return true;
    }

    /**
     * Find a single entity by its ID in the storage.
     *
     * @param string $table
     * @param int $id
     *
     * @return \Blixt\Persistence\Record|null
     * @throws \Blixt\Exceptions\StorageException
     */
    public function find(string $table, int $id): ?Record
    {
        $this->assertTableExists($table);

        $attributes = $this->data[$table][$id] ?? null;

        return $attributes !== null ? new Record($id, $attributes) : null;
    }

    /**
     * Get one or more entities from the storage with the given conditions. Always returns an array of Record objects.
     *
     * @param string $table
     * @param array $conditions
     * @param int $offset
     * @param int|null $limit
     *
     * @return array
     * @throws \Blixt\Exceptions\StorageException
     */
    public function getWhere(string $table, array $conditions, int $offset = 0, ?int $limit = null): array
    {
        $this->assertTableExists($table);

        $items = array_filter($this->data[$table], function ($attributes) use ($conditions) {
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    if (! in_array($attributes[$key], $value)) {
                        return false;
                    }
                } else {
                    if ($attributes[$key] != $value) {
                        return false;
                    }
                }
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        $items = array_slice($items, $offset, $limit, true);

        $keys = array_keys($items);

        return array_map(function ($attributes, $key) {
            return new Record($key, $attributes);
        }, $items, $keys);
    }

    /**
     * Create a new entity in the storage with the given set of attributes. Returns a Record object containing the new
     * ID of the record in the storage along with its attributes or null upon failure.
     *
     * @param string $table
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Record
     * @throws \Blixt\Exceptions\StorageException
     */
    public function create(string $table, array $attributes): Record
    {
        $this->assertTableExists($table);

        $id = $this->nextKey($table);

        $this->data[$table][$id] = $attributes;

        return new Record($id, $attributes);
    }

    /**
     * Update a single entity in the storage with the given set of attributes, identified by the given ID. Returns a
     * Record object containing the ID and the updated attributes or null upon failure.
     *
     * @param string $table
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Record
     * @throws \Blixt\Exceptions\StorageException
     */
    public function update(string $table, int $id, array $attributes): Record
    {
        $this->assertTableExists($table);

        if (! isset($this->data[$table][$id])) {
            throw new StorageException("Entity does not exist and cannot be updated.");
        }

        $this->data[$table][$id] = $attributes;

        return new Record($id, $attributes);
    }

    /**
     * Get the next key for the given table, incrementing the internal key value so that each subsequent call of nextKey
     * returns a number higher than the previous.
     *
     * @param string $table
     *
     * @return int
     */
    protected function nextKey($table): int
    {
        return $this->keys[$table]++;
    }

    /**
     * Ensure that the given table exists in the data, throwing an InvalidArgumentException if not.
     *
     * @param string $table
     *
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function assertTableExists($table): void
    {
        if (! isset($this->data[$table], $this->keys[$table])) {
            throw new StorageException("The table '{$table}' does not exist.");
        }
    }
}
