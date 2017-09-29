<?php

namespace Blixt\Storage;

use Blixt\Exceptions\UnspecifiedIndexException;
use InvalidArgumentException;

class SQLiteStorage extends AbstractStorage implements StorageInterface
{
    /**
     * The directory where the indexes are found.
     *
     * @var string
     */
    protected $directory;

    /**
     * The name of the index we're working with (a filename).
     *
     * @var string
     */
    protected $index;

    protected $pdo;

    /**
     * SQLiteStorage constructor.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->setDirectory($directory);
    }

    /**
     * The name of the index (a filename within the path) that we're working with.
     *
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->index = $index;

        // TODO - Disconnect PDO and set it to null
    }

    /**
     * Set the path
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
     * Get the path.
     *
     * @return string
     */
    public function getDirectory()
    {
        return rtrim($this->directory, '/\\');
    }

    /**
     * Get the index name.
     *
     * @return string
     * @throws \Blixt\Exceptions\UnspecifiedIndexException
     */
    public function getIndex()
    {
        if (empty($this->index)) {
            throw new UnspecifiedIndexException(
                'The index name must be provided.'
            );
        }

        return $this->index;
    }

    /**
     * Build a path to the index filename using the directory and index name.
     *
     * @return string
     */
    protected function buildPath()
    {
        return $this->getDirectory() . '/' . $this->getIndex();
    }

    /**
     * Tell if the store exists for the provided name.
     *
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->buildPath());
    }

    public function create()
    {

    }
}