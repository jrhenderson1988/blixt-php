<?php

namespace Blixt\Storage\Connectors;

use Blixt\Storage\Engines\SQLiteEngine;
use InvalidArgumentException;

class SQLiteConnector implements ConnectorInterface
{
    /**
     * The directory where the index files are located.
     *
     * @var string
     */
    protected $directory;

    /**
     * SQLiteBuilder constructor.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->setDirectory($directory);
    }

    /**
     * Set the directory where the index files are located.
     *
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        if (empty($directory)) {
            throw new InvalidArgumentException(
                'The provided directory may not be empty.'
            );
        }

        $this->directory = rtrim($directory, '/\\');
    }

    /**
     * Get the directory.
     *
     * @return string
     */
    protected function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Build a storage driver.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\Engines\SQLiteEngine
     */
    public function create($name)
    {
        return new SQLiteEngine(
            $this->getDirectory(), $name
        );
    }
}