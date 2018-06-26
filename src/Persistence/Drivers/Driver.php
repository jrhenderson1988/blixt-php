<?php

namespace Blixt\Persistence\Drivers;

use Blixt\Persistence\Entities\Entity;
use Illuminate\Support\Collection;

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
     * @param string $class
     * @param array  $conditions
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function findBy(string $class, array $conditions): ?Entity;

    /**
     * Find an entity in the storage by its primary key.
     *
     * @param string $class
     * @param int    $id
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function find(string $class, int $id): ?Entity;

    /**
     * Get one or many entities from the storage with the given conditions.
     *
     * @param string   $class
     * @param array    $conditions
     * @param int      $offset
     * @param int|null $limit
     *
     * @return \Illuminate\Support\Collection
     */
    public function getWhere(string $class, array $conditions, int $offset = 0, ?int $limit = null): Collection;

    /**
     * Get all of the entities from the storage with an optional offset and limit.
     *
     * @param string   $class
     * @param int      $offset
     * @param int|null $limit
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(string $class, int $offset = 0, ?int $limit = null): Collection;

    /**
     * Insert a new entity into the storage with the given set of attributes. The returned array must be the new set of
     * attributes, with the entity's key included.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function insert(Entity $entity): ?Entity;

    /**
     * Update an entity identified by the given key, in the storage with the given set of attributes. The returned array
     * must be the updated set of attributes.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function update(Entity $entity): ?Entity;
}