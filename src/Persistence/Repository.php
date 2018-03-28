<?php

namespace Blixt\Persistence;

use Blixt\Persistence\Drivers\Driver;
use Blixt\Storage\Entities\Entity;

class Repository
{
    /**
     * @var \Blixt\Persistence\Drivers\Driver
     */
    protected $driver;

    /**
     * @var string
     */
    protected $table;

    /**
     * Repository constructor.
     *
     * @param \Blixt\Persistence\Drivers\Driver $driver
     * @param string                            $table
     */
    public function __construct(Driver $driver, string $table)
    {
        $this->driver = $driver;
        $this->table = $table;
    }

    public function find(int $id): Entity
    {
        return $this->driver->find($this->table, $id);
    }
}