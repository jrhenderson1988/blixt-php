<?php

namespace Blixt\Storage;

interface FactoryInterface
{
    /**
     * Create a storage engine for the given index name.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\EngineInterface
     */
    public function create($name);
}