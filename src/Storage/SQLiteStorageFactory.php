<?php

namespace Blixt\Storage;

use InvalidArgumentException;

class SQLiteStorageFactory implements StorageFactoryInterface
{
    /**
     * The path from which index files should be loaded.
     *
     * @var string
     */
    protected $path;

    /**
     * SQLiteStorageFactory constructor.
     *
     * @param $path
     */
    public function __construct($path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException(
                'The path provided to SQLiteStorage must be a valid directory.'
            );
        }

        $this->path = rtrim($path, '/\\');
    }

    /**
     * Create an SQLiteStorage implementation with the given index name.
     *
     * @param string $name
     *
     * @return \Blixt\Storage\SQLiteStorage
     */
    public function create($name)
    {
        return new SQLiteStorage($this->path, $name);
    }
}