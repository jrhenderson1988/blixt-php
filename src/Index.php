<?php

namespace Blixt;

use Blixt\Storage\Connectors\ConnectorInterface;

class Index
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Blixt\Storage\Engines\EngineInterface
     */
    protected $storage;

    /**
     * Index constructor.
     *
     * @param string                                       $name
     * @param \Blixt\Storage\Connectors\ConnectorInterface $connector
     */
    public function __construct($name, ConnectorInterface $connector)
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
}