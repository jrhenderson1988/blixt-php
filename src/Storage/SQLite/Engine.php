<?php

namespace Blixt\Storage\SQLite;

use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Storage\EngineInterface;
use InvalidArgumentException;
use PDO;

class Engine implements EngineInterface
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
    protected $name;

    /**
     * The database connection.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * Does the database file currently exist?
     *
     * @var bool
     */
    protected $exists;

    /**
     * SQLiteStorage constructor.
     *
     * @param string $directory
     * @param string $name
     *
     * @throws \Blixt\Exceptions\UnspecifiedIndexException
     */
    public function __construct($directory, $name)
    {
        $this->setDirectory($directory);
        $this->setName($name);
        $this->checkExistence();
    }

    /**
     * Set the directory.
     *
     * @param string $directory
     */
    protected function setDirectory($directory)
    {
        if (empty($directory)) {
            throw new InvalidArgumentException(
                'The directory must be provided and not empty.'
            );
        }

        $this->directory = rtrim($directory, '/\\');
    }

    /**
     * Set the name.
     *
     * @param string $name
     */
    protected function setName($name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException(
                'The name must be provided and not empty.'
            );
        }

        $this->name = ltrim($name, '/\\');
    }

    /**
     * Get the directory.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Get the index name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Build a path to the index filename using the directory and index name.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getName();
    }

    /**
     * Given the full path name represented by the stored directory and path, check to see if the index file currently
     * exists and set the $exists property on this instance. This method is called in the constructor so that we are
     * able to see if the index file exists initially. We do this and use the $exists property to determine whether or
     * not we need to "create" the index, since blindly calling new PDO('sqlite:/path/to/file') would create it if it
     * did not exist. By doing this, we are able to throw an error if the index has not been created before it is used
     * and therefore force the create method to be called to make the index.
     *
     * @return bool
     */
    protected function checkExistence()
    {
        return $this->exists = file_exists(
            $this->getPath()
        );
    }

    /**
     * Tell if the store exists for the provided name.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->exists;
    }

    /**
     * Create the SQLite file and database connection for the name and path.
     *
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     */
    public function create()
    {
        if ($this->exists()) {
            throw new IndexAlreadyExistsException();
        }

        $this->connection();

        // After creating the index, check again for its existence. This will set our internal $exists property to true
        // and we will from now on be able to get a connection to the index. If creating the index somehow failed, this
        // would set our $exists property to false and methods requiring a connection would continue to fail.
        $this->checkExistence();
    }

    /**
     * Get the connection, creating it in the process if it has not yet been created.
     *
     * @return \PDO
     */
    protected function connection()
    {
        if (!$this->connection) {
            $this->connection = new PDO('sqlite:' . $this->getPath(), null, null, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }

        return $this->connection;
    }

    /**
     * Disconnect the connection by setting it to null.
     */
    public function disconnect()
    {
        $this->connection = null;
    }

    /**
     * Destroy the storage represented by the engine.
     */
    public function destroy()
    {
        $this->disconnect();

        return unlink(
            $this->getPath()
        );
    }
}