<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Drivers\Storage;
use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Record;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class Repository
{
    public const TABLE = '';

    /**
     * @var \Blixt\Persistence\Drivers\Storage
     */
    protected $driver;

    /**
     * Repository constructor.
     *
     * @param \Blixt\Persistence\Drivers\Storage $driver
     */
    public function __construct(Storage $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the driver.
     *
     * @return \Blixt\Persistence\Drivers\Storage
     */
    protected function driver(): Storage
    {
        return $this->driver;
    }

    /**
     * Get the name of the table that this repository represents.
     *
     * @return string
     */
    public static function table(): string
    {
        return static::TABLE;
    }

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    abstract public static function toAttributes(Entity $entity): array;

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Entity
     */
    abstract public static function toEntity(int $id, array $attributes): Entity;

    /**
     * Get the ID from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return int
     */
    protected static function getId(Entity $entity): int
    {
        return $entity->getId();
    }

    /**
     * Convert a Record into a relevant entity.
     *
     * @param \Blixt\Persistence\Record $record
     *
     * @return \Blixt\Persistence\Entities\Entity
     */
    protected static function fromRecord(Record $record): Entity
    {
        return static::toEntity($record->getId(), $record->getAttributes());
    }

    /**
     * Map an array of Record objects into a Collection of relevant entities.
     *
     * @param array $items
     *
     * @return \Illuminate\Support\Collection
     */
    protected static function toCollection(array $items): Collection
    {
        return Collection::make($items)->map(function (Record $record) {
            return static::fromRecord($record);
        });
    }

    /**
     * Get a collection of entities, represented by this repository where the given conditions are met. An optional
     * offset and limit may be provided.
     *
     * @param array $conditions
     * @param int $offset
     * @param int|null $limit
     *
     * @return \Illuminate\Support\Collection
     */
    public function getWhere(array $conditions, int $offset = 0, ?int $limit = null): Collection
    {
        return static::toCollection(
            $this->driver()->getWhere(static::table(), $conditions, $offset, $limit)
        );
    }

    /**
     * Get a collection of entities by their IDs.
     *
     * @param array $ids
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(array $ids): Collection
    {
        return static::toCollection(
            $this->driver()->get(static::table(), $ids)
        );
    }

    /**
     * Get a collection of entities, represented by this repository. An optional offset and limit may be provided.
     *
     * @param int $offset
     * @param int|null $limit
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(int $offset = 0, ?int $limit = null): Collection
    {
        return $this->getWhere([], $offset, $limit);
    }

    /**
     * Find a single entity by the given conditions.
     *
     * @param array $conditions
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function findBy(array $conditions): ?Entity
    {
        $items = $this->getWhere($conditions, 0, 1);

        if ($items->isNotEmpty() && ($record = $items->first()) instanceof Entity) {
            return $record;
        }

        return null;
    }

    /**
     * Find a single entity by its ID.
     *
     * @param int $id
     *
     * @return \Blixt\Persistence\Entities\Entity|null
     */
    public function find(int $id): ?Entity
    {
        $items = $this->get([$id]);

        if ($items->isNotEmpty() && ($record = $items->first()) instanceof Entity) {
            return $record;
        }

        return null;
    }

    /**
     * Create the given entity in the storage using the driver provided. If the given entity already exists (has an ID)
     * then an InvalidArgumentException is thrown. If a problem occurs persisting the entity to storage, a
     * StorageException is thrown. The entity's ID is set and it is returned upon success.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return \Blixt\Persistence\Entities\Entity
     * @throws \InvalidArgumentException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function create(Entity $entity): Entity
    {
        if ($entity->exists()) {
            throw new InvalidArgumentException("That entity already exists.");
        }

        return static::fromRecord(
            $this->driver()->create(static::table(), static::toAttributes($entity))
        );
    }

    /**
     * Update the given entity in the storage using the driver provided. If the given entity does not exist (does not
     * have an ID) then an InvalidArgumentException is thrown. If a problem occur persisting the entity to storage, a
     * StorageException is thrown. The entity is returned upon success.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return \Blixt\Persistence\Entities\Entity
     * @throws \InvalidArgumentException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function update(Entity $entity): Entity
    {
        if (! $entity->exists()) {
            throw new InvalidArgumentException("That entity does not exist.");
        }

        return static::fromRecord(
            $this->driver()->update(static::table(), static::getId($entity), static::toAttributes($entity))
        );
    }

    /**
     * Save the given entity to the storage using the driver provided. If the entity exists (has an ID), it is updated
     * in the storage and returned. If the entity does not exist (does not have an ID), it is inserted and its new ID is
     * set before being returned.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return \Blixt\Persistence\Entities\Entity
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Entity $entity): Entity
    {
        return $entity->exists() ? $this->update($entity) : $this->create($entity);
    }
}
