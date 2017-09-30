<?php

namespace Blixt\Storage;

use Blixt\Storage\Drivers\SQLiteDriver;
use InvalidArgumentException;

class SQLiteBuilder implements BuilderInterface
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
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(
                'The provided directory does not exist.'
            );
        }

        $this->directory = rtrim($directory, '/\\');
    }

    /**
     * Build a storage driver.
     *
     * @return \Blixt\Storage\Drivers\SQLiteDriver
     */
    public function build()
    {
        return new SQLiteDriver();
    }
}