<?php

namespace Blixt;

use Blixt\Storage\StorageFactoryInterface;

class Index
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Blixt\Storage\StorageDriverInterface
     */
    protected $storage;

    /**
     * Index constructor.
     *
     * @param string                                 $name
     * @param \Blixt\Storage\StorageFactoryInterface $factory
     */
    public function __construct($name, StorageFactoryInterface $factory)
    {
        $this->name = $name;
        $this->storage = $factory->create($name);

        $this->install();
    }

    protected function install()
    {
        if (!$this->storage->exists()) {
            $this->storage->create();
        }
    }
}