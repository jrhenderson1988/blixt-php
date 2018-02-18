<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Exceptions\StorageException;
use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Entity;
use Illuminate\Support\Collection;

abstract class AbstractRepository
{
    const ENTITY = '';
    const TABLE = '';
    const FIELD_ID = 'id';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * AbstractRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param int   $key
     * @param array $attributes
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    abstract protected function map($key, array $attributes);

    /**
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return array
     */
    abstract protected function getAttributes(Entity $entity);

    /**
     * @param array $items
     *
     * @return \Illuminate\Support\Collection
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function mapMany(array $items)
    {
        $results = new Collection();

        foreach ($items as $key => $row) {
            $this->assertInstanceOfEntity(
                $item = $this->map($key, $row)
            );

            $results->put($key, $item);
        }

        return $results;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function allEntities()
    {
        return $this->mapMany($this->storage->all(static::TABLE));
    }

    /**
     * @param array $conditions
     *
     * @return \Blixt\Storage\Entities\Entity|null
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function findEntityBy(array $conditions)
    {
        $items = $this->storage->getWhere(static::TABLE, $conditions);

        if (count($items) > 0) {
            reset($items);

            $this->assertInstanceOfEntity(
                $item = $this->map($id = key($items), $items[$id])
            );

            return $item;
        }

        return null;
    }

    /**
     * @param int|mixed $key
     *
     * @return \Blixt\Storage\Entities\Entity|null
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function findEntityById($key)
    {
        return $this->findEntityBy([
            static::FIELD_ID => $key
        ]);
    }

    /**
     * @param array $conditions
     *
     * @return \Illuminate\Support\Collection
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function getEntitiesBy(array $conditions)
    {
        return $this->mapMany($this->storage->getWhere(static::TABLE, $conditions));
    }

    /**
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return \Blixt\Storage\Entities\Entity
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function saveEntity(Entity $entity)
    {
        $saved = $entity->exists() ? $this->updateEntity($entity) : $this->createEntity($entity);

        $this->assertInstanceOfEntity($saved);

        return $saved;
    }

    /**
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    protected function createEntity(Entity $entity)
    {
        $attributes = $this->getAttributes($entity);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    protected function updateEntity(Entity $entity)
    {
        $attributes = $this->getAttributes($entity);

        $this->storage->update(static::TABLE, $entity->getId(), $attributes);

        return $entity;
    }

    /**
     * @param mixed $value
     *
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function assertInstanceOfEntity($value)
    {
        if (! is_a($value, static::ENTITY)) {
            throw new StorageException('Expected instance of ' . static::ENTITY . '.');
        }
    }
}