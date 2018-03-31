<?php

namespace Blixt\Persistence\Drivers;

use Blixt\Persistence\Record;

interface Driver
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
    public function create(): bool;

    /**
     * Find an entity in the storage by the given field/value combination.
     *
     * @param string $table
     * @param int    $field
     * @param mixed  $value
     *
     * @return array|null
     */
    public function findBy(string $table, int $field, $value): ?Record;

    /**
     * Find an entity in the storage by its primary key.
     *
     * @param string $table
     * @param int    $key
     *
     * @return array|null
     */
    public function find(string $table, int $key): ?Record;

    /**
     * Get one or many entities from the storage with the given conditions.
     *
     * @param string   $table
     * @param array    $conditions
     * @param int      $offset
     * @param int|null $limit
     *
     * @return array|null
     */
    public function getWhere(string $table, array $conditions, int $offset = 0, ?int $limit = null): ?array;

    /**
     * Insert a new entity into the storage with the given set of attributes. The returned array must be the new set of
     * attributes, with the entity's key included.
     *
     * @param string $table
     * @param array  $attributes
     *
     * @return \Blixt\Persistence\Record
     */
    public function insert(string $table, array $attributes): Record;

    /**
     * Update an entity identified by the given key, in the storage with the given set of attributes. The returned array
     * must be the updated set of attributes.
     *
     * @param string $table
     * @param int    $key
     * @param array  $attributes
     *
     * @return \Blixt\Persistence\Record
     */
    public function update(string $table, int $key, array $attributes): Record;
}