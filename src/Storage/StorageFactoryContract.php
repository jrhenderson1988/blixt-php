<?php

namespace Blixt\Storage;

interface StorageFactoryContract
{
    /**
     * Create a storage engine for the given index name.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\StorageEngineContract
     */
    public function create($name);
}