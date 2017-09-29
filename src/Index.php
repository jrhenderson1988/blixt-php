<?php

namespace Blixt;

use Blixt\Storage\StorageFactoryInterface;

class Index
{
    protected $name;
    protected $storage;

    public function __construct($name, StorageFactoryInterface $storageFactory)
    {
        $this->name = $name;
        $this->storage = $storageFactory->create($name);

        $this->install();
    }

    protected function install()
    {

    }
}