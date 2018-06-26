<?php

namespace Blixt\Persistence\Drivers;

use Blixt\Exceptions\StorageException;
use Blixt\Persistence\Entities\Entity;
use Illuminate\Support\Collection;

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
     * Insert a new entity into the storage with the given set of attributes. The returned array must be the new set of
     * attributes, with the entity's key included.
     *
     * @param \Blixt\Persistence\Entities\Entity|null $entity
     *
     * @return \Blixt\Persistence\Entities\Entity
     * @throws \Blixt\Exceptions\StorageException
     */
    public function insert(Entity $entity): ?Entity
    {
        $this->assertTableExists($table = $this->getTableFromEntity($entity));

        $entity->setId($id = $this->nextKey($table));

        $this->data[$table][$id] = $entity->toArray();

        return $entity;
    }

    /**
     * Update an entity identified by the given key, in the storage with the given set of attributes. The returned array
     * must be the updated set of attributes.
     *
     * @param \Blixt\Persistence\Entities\Entity|null $entity
     *
     * @return \Blixt\Persistence\Entities\Entity
     * @throws \Blixt\Exceptions\StorageException
     */
    public function update(Entity $entity): ?Entity
    {
        $this->assertTableExists($table = $this->getTableFromEntity($entity));

        if (! isset($this->data[$table][$id = $entity->getId()])) {
            throw new StorageException('Entity does not exist and cannot be updated.');
        }

        $this->data[$table][$id] = $entity->toArray();

        return $entity;
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

    /**
     * Find an entity in the storage by the given field/value combination.
     *
     * @param string $class
     * @param array  $conditions
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function findBy(string $class, array $conditions): ?Entity
    {
        return $this->getWhere($class, $conditions, 0, 1)->first();
    }

    /**
     * Find an entity in the storage by its primary key.
     *
     * @param string $class
     * @param int    $id
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function find(string $class, int $id): ?Entity
    {
        return $this->findBy($class, [Entity::FIELD_ID => $id]);
    }

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
    public function getWhere(string $class, array $conditions, int $offset = 0, ?int $limit = null): Collection
    {
        $table = $this->getTableFromEntityClassName($class);

        return Collection::make($this->data[$table])->filter(function ($item) use ($conditions) {
            foreach ($conditions as $key => $value) {
                $value = is_array($value) ? Collection::make($value) : $value;

                if ($value instanceof Collection) {
                    if (! $value->contains($item[$key])) {
                        return false;
                    }
                } else {
                    if ($item[$key] != $value) {
                        return false;
                    }
                }
            }

            return true;
        })->slice($offset, $limit)->map(function ($item) use ($class) {
            return $class::fromArray($item);
        });
    }

    /**
     * Get all of the entities from the storage with an optional offset and limit.
     *
     * @param string   $class
     * @param int      $offset
     * @param int|null $limit
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(string $class, int $offset = 0, ?int $limit = null): Collection
    {
        return $this->getWhere($class, [], $offset, $limit);
    }
}