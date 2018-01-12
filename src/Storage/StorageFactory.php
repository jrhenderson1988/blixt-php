<?php

namespace Blixt\Storage;

interface StorageFactory
{
    /**
     * Create a storage engine for the given index name.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\Storage
     */
    public function create($name);
}