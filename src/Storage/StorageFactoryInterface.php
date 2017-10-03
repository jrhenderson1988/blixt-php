<?php

namespace Blixt\Storage;

interface StorageFactoryInterface
{
    /**
     * Build a storage driver.
     *
     * @return \Blixt\Storage\Drivers\StorageDriverInterface
     */
    public function create();
}