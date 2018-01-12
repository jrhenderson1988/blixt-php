<?php

namespace Blixt\Storage\SQLite;

use Blixt\Storage\StorageFactory;
use InvalidArgumentException;

class SQLiteStorageFactory implements StorageFactory
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
     * @return \Blixt\Storage\SQLite\SQLiteStorage
     */
    public function create($name)
    {
        return new SQLiteStorage(
            $this->getDirectory(), $name
        );
    }
}