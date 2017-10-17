<?php

namespace Blixt;

use Blixt\Documents\Document;
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

    public function addDocument(Document $document)
    {
        return $this->storage->addDocument($document);
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