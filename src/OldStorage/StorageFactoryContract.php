<?php

namespace Blixt\Storage;

interface StorageFactoryContract
{
    /**
     * Create a storage engine for the given index name.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\StorageContract
     */
    public function create($name);
}