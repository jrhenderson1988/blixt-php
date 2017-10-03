<?php

namespace Blixt\Storage;

interface StorageFactoryInterface
{
    /**
     * Build a storage driver.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\StorageDriverInterface
     */
    public function create($name);
}