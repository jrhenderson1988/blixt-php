<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Drivers\Driver;

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
    protected function driver()
    {
        return $this->driver;
    }
}