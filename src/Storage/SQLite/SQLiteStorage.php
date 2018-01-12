<?php

namespace Blixt\Storage\SQLite;

use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Exceptions\StorageException;
use Blixt\Index\Schema\Schema;
use Blixt\Models\Column;
use Blixt\Models\Document;
use Blixt\Models\Field;
use Blixt\Models\Presence;
use Blixt\Models\Word;
use Blixt\Storage\AbstractStorage;
use Blixt\Storage\Storage;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class SQLiteStorage extends AbstractStorage implements Storage
{
    /**
     * The directory where the indexes are found.
     *
     * @var string
     */
    protected $directory;

    /**
     * The name of the index we're working with (a filename without the extension).
     *
     * @var string
     */
    protected $name;

    /**
     * The database connection.
     *
     * @var \Blixt\Storage\SQLite\SQLiteConnection
     */
    protected $connection;

    /**
     * A mapper object to transform rows of data in array/stdClass format into the relevant models.
     *
     * @var \Blixt\Storage\SQLite\SQLiteMapper
     */
    protected $mapper;

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
     */
    public function __construct($directory, $name)
    {
        $this->setDirectory($directory);
        $this->setName($name);
        $this->checkExistence();
        $this->mapper = new SQLiteMapper();
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

//    /**
//     * Execute the provided callable in a transaction. The return value of the callable is returned from this method. If
//     * any exceptions are thrown within the callable, the transaction is rolled back.
//     *
//     * @param callable $callable
//     *
//     * @return mixed
//     * @throws \Exception
//     */
//    public function transaction(callable $callable)
//    {
//        $this->connection()->beginTransaction();
//
//        try {
//            $response = call_user_func($callable, $this);
//
//            $this->connection()->commitTransaction();
//
//            return $response;
//        } catch (Exception $ex) {
//            $this->connection()->rollBackTransaction();
//
//            throw $ex;
//        }
//    }

    /**
     * Create the SQLite file and database connection for the name and path.
     *
     * @param \Blixt\Index\Schema\Schema $schema
     *
     * @return bool
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     */
    public function create(Schema $schema)
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

            'CREATE UNIQUE INDEX "uq_columns_name" ON "columns" ("name");',

            'CREATE TABLE "documents" (' .
            ' "id" INTEGER PRIMARY KEY,' .
            ' "key" TEXT NOT NULL' .
            ');',

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

        // Execute the statements to create the tables and indices.
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
     * @return \Blixt\Storage\SQLite\SQLiteConnection
     */
    protected function connection()
    {
        if (!$this->connection) {
            $this->connection = new SQLiteConnection(
                $this->getPath()
            );
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
     * Find a word by the given ID.
     *
     * @param string $id
     *
     * @return \Blixt\Models\Word|null
     */
    public function getWordById($id)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "words" WHERE "id" = ? LIMIT 1', [$id]
        );

        return $result ? $this->mapper->word($result) : null;
    }

    /**
     * Find a word by the given word string.
     *
     * @param string $word
     *
     * @return \Blixt\Models\Word|null
     */
    public function getWordByWord($word)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "words" WHERE "word" = ? LIMIT 1', [$word]
        );

        return $result ? $this->mapper->word($result) : null;
    }

    /**
     * Create a word, given a word string.
     *
     * @param string $word
     *
     * @return \Blixt\Models\Word
     * @throws \Blixt\Exceptions\StorageException
     */
    public function createWord($word)
    {
        $id = $this->connection()->insert(
            'INSERT INTO "words" ("word") VALUES (?)', [$word]
        );

        if ($id === false) {
            throw new StorageException(
                'A problem occurred creating a Word.'
            );
        }

        return $this->mapper->word(['id' => $id, 'word' => $word]);
    }

    /**
     * Get a column by its ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Models\Column
     */
    public function getColumnById($id)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "columns" WHERE "id" = ? LIMIT 1', [$id]
        );

        return $result ? $this->mapper->column($result) : null;
    }

    /**
     * Get a column by its name.
     *
     * @param int|string $name
     *
     * @return \Blixt\Models\Column
     */
    public function getColumnByName($name)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "columns" WHERE "name" = ? LIMIT 1', [$name]
        );

        return $result ? $this->mapper->column($result) : null;
    }

    /**
     * Load all of the columns from the storage as a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllColumns()
    {
        $results = $this->connection()->select(
            'SELECT * FROM "columns"'
        );

        return $this->mapper->columns($results);
    }

    /**
     * Create a column with the given parameters.
     *
     * @param string|mixed  $name
     * @param boolean|mixed $indexed
     * @param boolean|mixed $stored
     * @param float|mixed   $weight
     *
     * @return \Blixt\Models\Column
     * @throws \Blixt\Exceptions\StorageException
     */
    public function createColumn($name, $indexed, $stored, $weight)
    {
        $id = $this->connection()->insert(
            'INSERT INTO "columns" ("name", "indexed", "stored", "weight") VALUES (?, ?, ?, ?)',
            [$name, !! $indexed ? 1 : 0, !! $stored ? 1 : 0, $weight]
        );

        if ($id === false) {
            throw new StorageException(
                'A problem occurred creating a Column.'
            );
        }

        return $this->mapper->column([
            'id' => $id,
            'name' => $name,
            'indexed' => $indexed,
            'stored' => $stored,
            'weight' => $weight
        ]);
    }

    /**
     * Find a document in the storage by the given ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Models\Document|null
     */
    public function getDocumentById($id)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "documents" WHERE "id" = ? LIMIT 1', [$id]
        );

        return $result ? $this->mapper->document($result) : null;
    }

    /**
     * Find a document by the provided key.
     *
     * @param mixed $key
     *
     * @return \Blixt\Models\Document|null
     */
    public function getDocumentByKey($key)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "documents" WHERE "key" = ? LIMIT 1', [$key]
        );

        return $result ? $this->mapper->document($result) : null;
    }

    /**
     * Create a document with the given key.
     *
     * @param mixed $key
     *
     * @return \Blixt\Models\Document
     * @throws \Blixt\Exceptions\StorageException
     */
    public function createDocument($key)
    {
        $id = $this->connection()->insert(
            'INSERT INTO "documents" ("key") VALUES (?)', [$key]
        );

        if ($id === false) {
            throw new StorageException(
                'A problem occurred creating a Document.'
            );
        }

        return $this->mapper->document([
            'id' => $id,
            'key' => $key
        ]);
    }

    /**
     * Find a field in the storage by the given ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Models\Field
     */
    public function getFieldById($id)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "fields" WHERE "id" = ? LIMIT 1', [$id]
        );

        return $result ? $this->mapper->field($result) : null;
    }

    /**
     * Find a field in the storage by the given document and column.
     *
     * @param \Blixt\Models\Document $document
     * @param \Blixt\Models\Column   $column
     *
     * @return \Blixt\Models\Field
     */
    public function getFieldByDocumentAndColumn(Document $document, Column $column)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "fields" WHERE "document_id" = ? AND "column_id" = ? LIMIT 1',
            [$document->getId(), $column->getId()]
        );

        return $result ? $this->mapper->field($result) : null;
    }

    /**
     * Get all the fields in the storage for the given document.
     *
     * @param \Blixt\Models\Document $document
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllFieldsByDocument(Document $document)
    {
        $results = $this->connection()->select(
            'SELECT * FROM "fields" WHERE "document_id" = ?', [$document->getId()]
        );

        return $this->mapper->fields($results);
    }

    /**
     * Create a field for the given document and column, with the given value.
     *
     * @param \Blixt\Models\Document $document
     * @param \Blixt\Models\Column   $column
     * @param mixed|null             $value
     *
     * @return \Blixt\Models\Field
     * @throws \Blixt\Exceptions\StorageException
     */
    public function createField(Document $document, Column $column, $value = null)
    {
        $id = $this->connection()->insert(
            'INSERT INTO "fields" ("document_id", "column_id", "value") VALUES (?, ?, ?)',
            [$document->getId(), $column->getId(), $value]
        );

        if ($id === false) {
            throw new StorageException(
                'A problem occurred creating a Field.'
            );
        }

        return $this->mapper->field([
            'id' => $id,
            'document_id' => $document->getId(),
            'column_id' => $column->getId(),
            'value' => $value
        ]);
    }


    /**
     * Find a presence by its ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Models\Presence
     */
    public function getPresenceById($id)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "presences" WHERE "id" = ? LIMIT 1', [$id]
        );

        return $result ? $this->mapper->presence($result) : null;
    }

    /**
     * Find a presence by the field and word provided.
     *
     * @param \Blixt\Models\Field $field
     * @param \Blixt\Models\Word  $word
     *
     * @return \Blixt\Models\Presence
     */
    public function getPresenceByFieldAndWord(Field $field, Word $word)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "presences" WHERE "field_id" = ? AND "word_id" = ? LIMIT 1',
            [$field->getId(), $word->getId()]
        );

        return $result ? $this->mapper->presence($result) : null;
    }

    /**
     * Find the presences associated with the given field.
     *
     * @param \Blixt\Models\Field $field
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPresencesByField(Field $field)
    {
        $results = $this->connection()->select(
            'SELECT * FROM "presences" WHERE "field_id" = ?', [$field->getId()]
        );

        return $this->mapper->presences($results);
    }

    /**
     * Create a presence, which represents the presence of a word in a field and the corresponding frequency.
     *
     * @param \Blixt\Models\Field $field
     * @param \Blixt\Models\Word  $word
     * @param int|string          $frequency
     *
     * @return \Blixt\Models\Presence
     * @throws \Blixt\Exceptions\StorageException
     */
    public function createPresence(Field $field, Word $word, $frequency)
    {
        $id = $this->connection()->insert(
            'INSERT INTO "presences" ("field_id", "word_id", "frequency") VALUES (?, ?, ?)',
            [$field->getId(), $word->getId(), $frequency]
        );

        if ($id === false) {
            throw new StorageException(
                'A problem occurred creating a Presence.'
            );
        }

        return $this->mapper->presence([
            'id' => $id,
            'field_id' => $field->getId(),
            'word_id' => $word->getId(),
            'frequency' => $frequency
        ]);
    }

    /**
     * Find an occurrence by the given ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Models\Occurrence
     */
    public function getOccurrenceById($id)
    {
        $result = $this->connection()->selectOne(
            'SELECT * FROM "occurrences" WHERE "id" = ? LIMIT 1', [$id]
        );

        return $result ? $this->mapper->occurrence($result) : null;
    }

    /**
     * @param \Blixt\Models\Presence $presence
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllOccurrencesByPresence(Presence $presence)
    {
        $results = $this->connection()->select(
            'SELECT * FROM "occurrences" WHERE "presence_id" = ?', [$presence->getId()]
        );

        return $this->mapper->occurrences($results);
    }

    /**
     * Create an occurrence record, which represents a presence and the position that it appeared in its field.
     *
     * @param \Blixt\Models\Presence $presence
     * @param int                    $position
     *
     * @return \Blixt\Models\Occurrence
     * @throws \Blixt\Exceptions\StorageException
     */
    public function createOccurrence(Presence $presence, $position)
    {
        $id = $this->connection()->insert(
            'INSERT INTO "occurrences" ("presence_id", "position") VALUES (?, ?)', [$presence->getId(), $position]
        );

        if ($id === false) {
            throw new StorageException(
                'A problem occurred creating an Occurrence.'
            );
        }

        return $this->mapper->occurrence([
            'id' => $id,
            'presence_id' => $presence->getId(),
            'position' => $position
        ]);
    }
}