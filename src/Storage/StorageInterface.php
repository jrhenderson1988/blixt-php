<?php

namespace Blixt\Storage;

interface StorageInterface
{
    /**
     * Tell if the store exists for the provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name);
    public function create($name);
}