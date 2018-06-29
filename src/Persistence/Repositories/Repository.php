<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Exceptions\StorageException;
use Blixt\Persistence\Drivers\Driver;
use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Record;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class Repository
{
    /**
     * @var \Blixt\Persistence\Drivers\Driver
     */
    protected $driver;

    /**
     * Repository constructor.
     *
     * @param \Blixt\Persistence\Drivers\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the driver.
     *
     * @return \Blixt\Persistence\Drivers\Driver
     */
    protected function driver(): Driver
    {
        return $this->driver;
    }

    /**
     * Get the name of the table that this repository represents.
     *
     * @return string
     */
    protected abstract function table(): string;

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    protected abstract function getAttributes(Entity $entity): array;

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Entity
     */
    protected abstract function toEntity(int $id, array $attributes): Entity;

    /**
     * Get the ID from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Entity $entity
     *
     * @return int
     */
    protected function getId(Entity $entity): int
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
    protected function fromRecord(Record $record): Entity
    {
        return $this->toEntity($record->getId(), $record->getAttributes());
    }

    /**
     * Map an array of Record objects into a Collection of relevant entities.
     *
     * @param array $items
     *
     * @return \Illuminate\Support\Collection
     */
    protected function toCollection(array $items): Collection
    {
        return Collection::make($items)->map(function (Record $record) {
            return $this->fromRecord($record);
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
        return $this->toCollection(
            $this->driver()->getWhere($this->table(), $conditions, $offset, $limit)
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
    public function all(int $offset, ?int $limit): Collection
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
        $record = $this->driver()->findBy($this->table(), $conditions);

        return $record !== null ? $this->fromRecord($record) : null;
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
        $record = $this->driver()->find($this->table(), $id);

        return $record !== null ? $this->fromRecord($record) : null;
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

        return $this->fromRecord(
            $this->driver()->create($this->table(), $this->getAttributes($entity))
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

        return $this->fromRecord(
            $this->driver()->update($this->table(), $this->getId($entity), $this->getAttributes($entity))
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