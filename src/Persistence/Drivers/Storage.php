<?php

namespace Blixt\Persistence\Drivers;

use Blixt\Persistence\Record;

/**
 * Storage driver interface. Each storage driver must manage its own IDs. A MySQL driver might decide that it will use
 * an auto-incrementing, integer primary key column called 'id' as an ID for the record. An SQLite driver may use the
 * in-built 'rowid' as the primary key which is itself an auto-incrementing integer. An in-memory, array driver might
 * use the numeric index of the array as the ID. When returning data, representing records from the driver, the Record
 * class must be used. This allows the driver to pass the record's ID separately to its attributes to allow the
 * repositories using the driver to distinguish between the items.
 *
 * @package Blixt\Persistence\Drivers
 */
interface Storage
{
    /**
     * Tell if the storage represented by the engine exists.
     *
     * @return boolean
     */
    public function exists(): bool;

    /**
     * Create the storage represented by the engine.
     *
     * @return bool
     */
    public function install(): bool;

    /**
     * Get one or more entities from the storage by the given set of IDs. Always returns an array of Record objects.
     *
     * @param string $table
     * @param array $ids
     *
     * @return array
     */
    public function get(string $table, array $ids): array;

    /**
     * Get one or more entities from the storage with the given conditions. Always returns an array of Record objects.
     *
     * @param string $table
     * @param array $conditions
     * @param int $offset
     * @param int|null $limit
     *
     * @return array
     */
    public function getWhere(string $table, array $conditions, int $offset = 0, ?int $limit = null): array;

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
    public function create(string $table, array $attributes): Record;

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
    public function update(string $table, int $id, array $attributes): Record;
}
