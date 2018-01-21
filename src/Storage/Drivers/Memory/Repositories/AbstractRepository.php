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
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    abstract protected function map(array $row);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        $results = new Collection();

        foreach ($this->storage->all(static::TABLE) as $key => $row) {
            $results->put($key, $this->map(array_merge([static::FIELD_ID => $key], $row)));
        }

        return $results;
    }

    /**
     * @param int $id
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    public function find($id)
    {
        if ($result = $this->storage->find(static::TABLE, $id)) {
            return $this->map($result);
        }

        return null;
    }

    /**
     * @param array $data
     *
     * @return \Blixt\Storage\Entities\Entity
     */
    public function insert(array $data)
    {
        $id = $this->storage->insert(static::TABLE, $data);

        return $this->map(array_merge($data, [static::FIELD_ID => $id]));
    }
}