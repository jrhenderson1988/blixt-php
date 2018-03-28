<?php

namespace Blixt\Persistence;

use Blixt\Persistence\Drivers\Driver;

class Factory
{
    /**
     * @var \Blixt\Persistence\Drivers\Driver
     */
    protected $driver;

    /**
     * @var array
     */
    protected $repositories = [];

    /**
     * StorageFactory constructor.
     *
     * @param \Blixt\Persistence\Drivers\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get or create a repository for the provided class name.
     *
     * @param string $class
     *
     * @return \Blixt\Persistence\Repository
     */
    public function repository(string $class): Repository
    {
        if (isset($this->repositories[$class])) {
            return $this->repositories[$class];
        }

        return $this->repositories[$class] = $this->makeRepository($class);
    }

    /**
     * Create a repository for the given class name.
     *
     * @param string $class
     *
     * @return \Blixt\Persistence\Repository
     */
    protected function makeRepository(string $class): Repository
    {
        return new Repository($this->driver, $this->getTableName($class));
    }

    /**
     * Get the table name of the given class. This is done by reading the TABLE constant defined on the Entity class.
     * If no such constant is defined, or is equates to false, then the class name itself is returned.
     *
     * @param string $class
     *
     * @return string
     */
    protected function getTableName(string $class): string
    {
        if ($table = constant($class . '::TABLE')) {
            return $table;
        }

        return $class;
    }
}