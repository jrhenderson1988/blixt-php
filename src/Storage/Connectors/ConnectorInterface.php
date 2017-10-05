<?php

namespace Blixt\Storage\Connectors;

interface ConnectorInterface
{
    /**
     * Build a storage driver.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\Engines\EngineInterface
     */
    public function create($name);
}