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
     * @var \Blixt\Storage\Connectors\ConnectorInterface
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

        $this->install();
    }

    protected function install()
    {
        if (!$this->storage->exists()) {
            $this->storage->create();
        }
    }
}