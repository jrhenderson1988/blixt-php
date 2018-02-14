<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Illuminate\Support\Collection;

abstract class AbstractRepository
{
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
     * Map an array, representing an entity into a relevant Entity object.
     *
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    abstract protected function map($key, array $row);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        $results = new Collection();

        foreach ($this->storage->all(static::TABLE) as $key => $row) {
            $results->put($key, $this->map($key, $row));
        }

        return $results;
    }

    /**
     * @param array $data
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    public function insert(array $data)
    {
        $id = $this->storage->insert(static::TABLE, $data);

        return $this->map($id, $data);
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    public function update($id, array $data)
    {
        $this->storage->update(static::TABLE, $id, $data);

        return $this->find($id);
    }

    /**
     * @param array $conditions
     *
     * @return \Blixt\Storage\Entities\Entity|null
     */
    public function findBy(array $conditions)
    {
        if ($item = $this->getWhere($conditions)->first()) {
            return $item;
        }

        return null;
    }

    /**
     * @param int $id
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    public function find($id)
    {
        if ($result = $this->storage->find(static::TABLE, $id)) {
            return $this->map($id, $result);
        }

        return null;
    }

    /**
     * @param array $conditions
     *
     * @return \Illuminate\Support\Collection
     */
    public function getWhere(array $conditions)
    {
        $collection = new Collection();

        foreach ($this->storage->getWhere(static::TABLE, $conditions) as $key => $result) {
            $collection->push($this->map($key, $result));
        }

        return $collection;
    }
}