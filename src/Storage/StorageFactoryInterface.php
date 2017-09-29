<?php

namespace Blixt\Storage;

interface StorageFactoryInterface
{
    /**
     * Create a storage implementation given the name of an index.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\StorageInterface
     */
    public function create($name);
}