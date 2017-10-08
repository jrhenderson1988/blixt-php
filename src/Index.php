<?php

namespace Blixt;

use Blixt\Storage\FactoryInterface as StorageFactory;

class Index
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Blixt\Storage\EngineInterface
     */
    protected $storage;

    /**
     * Index constructor.
     *
     * @param string                          $name
     * @param \Blixt\Storage\FactoryInterface $connector
     */
    public function __construct($name, StorageFactory $connector)
    {
        $this->name = $name;
        $this->storage = $connector->create($name);

        if (!$this->storage->exists()) {
            $this->storage->create();
        }
    }



    public function addDocument()
    {

    }

    public function addDocuments()
    {

    }

    public function search()
    {

    }

    public function destroy()
    {
        return $this->storage->destroy();
    }
}