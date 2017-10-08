<?php

namespace Blixt\Storage\Connectors;

interface ConnectorInterface
{
    /**
     * Create a storage engine for the given index name.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\Engines\EngineInterface
     */
    public function create($name);
}