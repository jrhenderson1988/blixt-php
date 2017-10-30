<?php

namespace Blixt\Storage\SQLite;

use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Models\Schema;
use Blixt\Storage\EngineInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;

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
     * @var \Blixt\Storage\SQLite\Connection
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
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getName() . '.index';
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

        $statements = new Collection([
            'CREATE TABLE "words" (' .
            ' "id" INTEGER PRIMARY KEY,' .
            ' "word" TEXT NOT NULL' .
            ');',

            'CREATE UNIQUE INDEX "uq_words_word" ON "words" ("word");',

            'CREATE TABLE "columns" (' .
            ' "id" INTEGER PRIMARY KEY,' .
            ' "name" TEXT NOT NULL,' .
            ' "indexed" INTEGER NOT NULL,' .
            ' "stored" INTEGER NOT NULL,' .
            ' "weight" INTEGER NOT NULL' .
            ');',

            'CREATE UNIQUE INDEX "uq_columns__name" ON "columns" ("name");',

            'CREATE TABLE "documents" (' .
            ' "id" INTEGER PRIMARY KEY,' .
            ' "key" TEXT NOT NULL' .
            ');' .

            'CREATE UNIQUE INDEX "uq_documents_key" ON "documents" ("key");',

            'CREATE TABLE "fields" (' .
            ' "id" INTEGER PRIMARY KEY,' .
            ' "document_id" INTEGER NOT NULL,' .
            ' "column_id" INTEGER NOT NULL,' .
            ' "value" TEXT' . // Maybe BLOB is better?
            ');',

            'CREATE UNIQUE INDEX "uq_fields_document_id_column_id" ON "fields" ("document_id", "column_id");',

            'CREATE TABLE "presences" (' .
            ' "id" INTEGER PRIMARY KEY,' .
            ' "field_id" INTEGER NOT NULL,' .
            ' "word_id" INTEGER NOT NULL,' .
            ' "frequency" INTEGER NOT NULL' .
            ');',

            'CREATE UNIQUE INDEX "uq_presences_field_id_word_id" ON "presences" ("field_id", "word_id");',

            'CREATE TABLE "occurrences" (' .
            ' "id" INTEGER PRIMARY KEY,' .
            ' "presence_id" INTEGER NOT NULL,' .
            ' "position" INTEGER NOT NULL' .
            ');',

            'CREATE INDEX "idx_occurrences_presence_id" ON "occurrences" ("presence_id");'
        ]);

        $statements->each(function ($statement) {
            $this->connection()->statement($statement);
        });

        // After creating the index, check again for its existence. This will set our internal $exists property to true
        // and we will from now on be able to get a connection to the index. If creating the index somehow failed, this
        // would set our $exists property to false and methods requiring a connection would continue to fail.
        $this->checkExistence();

        return true;
    }

    /**
     * Get the connection, creating it in the process if it has not yet been created.
     *
     * @return \Blixt\Storage\SQLite\Connection
     */
    protected function connection()
    {
        if (!$this->connection) {
            $this->connection = new Connection($this->getPath());
        }

        return $this->connection;
    }

    /**
     * Disconnect the connection by setting it to null.
     *
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

    /**
     * Begin a transaction for the storage engine.
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connection()->beginTransaction();
    }

    /**
     * Roll back the current transaction for the storage engine.
     *
     * @return bool
     */
    public function rollBackTransaction()
    {
        return $this->connection()->rollBackTransaction();
    }

    /**
     * Commit the current transaction for the storage engine.
     *
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->connection()->commitTransaction();
    }

    public function findSchemaByName($name)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "schemas" WHERE "name" = ?', [$name]
        );

        return is_array($result) ? new Schema($result['id'], $result['name']) : null;
    }

    public function createSchema($name)
    {
        $id = $this->connection()->insert(
            'INSERT INTO "schemas" ("name") VALUES (?)', [$name]
        );

        return $id !== false ? new Schema($id, $name) : null;
    }
}