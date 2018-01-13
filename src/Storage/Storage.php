<?php

namespace Blixt\Storage;

use Blixt\Index\Schema\Schema;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Field;
use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Entities\Word;

interface Storage
{
    /**
     * Tell if the storage represented by the engine exists.
     *
     * @return boolean
     */
    public function exists();

    /**
     * Create the storage represented by the engine.
     *
     * @return bool
     */
    public function create();

    /**
     * Destroy the storage represented by the engine.
     *
     * @return boolean
     */
    public function destroy();

    /**
     * Execute the provided closure in a transaction. The return value of the closure is returned from this method. If
     * any exceptions are thrown within the closure, the transaction is rolled back.
     *
     * @param callable $callable
     *
     * @return mixed
     */
    public function transaction(callable $callable);

    public function getSchemaById($id);
    public function getSchemaByName($name);
    public function getAllSchemas();
    public function createSchema($name);

    /**
     * Find a word by the given ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Storage\Entities\Word|null
     */
    public function getWordById($id);

    /**
     * Find a word by the given word string.
     *
     * @param string $word
     *
     * @return \Blixt\Storage\Entities\Word|null
     */
    public function getWordByWord($word);

    /**
     * Create a word, given a word string.
     *
     * @param string $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function createWord($word);

    /**
     * Get a column by its ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function getColumnById($id);

    /**
     * Get a column by its name.
     *
     * @param int|string $name
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function getColumnByName($name);

    /**
     * Load all of the columns from the storage as a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllColumns();

    /**
     * Create a column with the given parameters.
     *
     * @param string|mixed  $name
     * @param boolean|mixed $indexed
     * @param boolean|mixed $stored
     * @param float|mixed   $weight
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function createColumn($name, $indexed, $stored, $weight);

    /**
     * Find a document in the storage by the given ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Storage\Entities\Document|null
     */
    public function getDocumentById($id);

    /**
     * Find a document in the storage by the given key.
     *
     * @param mixed $key
     *
     * @return \Blixt\Storage\Entities\Document|null
     */
    public function getDocumentByKey($key);

    /**
     * Create a document in the storage with the given key.
     *
     * @param mixed $key
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public function createDocument($key);

    /**
     * Find a field in the storage by the given ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function getFieldById($id);

    /**
     * Find a field in the storage by the given document and column.
     *
     * @param \Blixt\Storage\Entities\Document $document
     * @param \Blixt\Storage\Entities\Column   $column
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function getFieldByDocumentAndColumn(Document $document, Column $column);

    /**
     * Get all the fields in the storage for the given document.
     *
     * @param \Blixt\Storage\Entities\Document $document
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllFieldsByDocument(Document $document);

    /**
     * Create a field for the given document and column, with the given value.
     *
     * @param \Blixt\Storage\Entities\Document $document
     * @param \Blixt\Storage\Entities\Column   $column
     * @param mixed|null                       $value
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function createField(Document $document, Column $column, $value = null);

    /**
     * Find a presence by its ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function getPresenceById($id);

    /**
     * Find a presence by the field and word provided.
     *
     * @param \Blixt\Storage\Entities\Field $field
     * @param \Blixt\Storage\Entities\Word  $word
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function getPresenceByFieldAndWord(Field $field, Word $word);

    /**
     * Find the presences associated with the given field.
     *
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPresencesByField(Field $field);

    /**
     * Create a presence, which represents the presence of a word in a field and the corresponding frequency.
     *
     * @param \Blixt\Storage\Entities\Field $field
     * @param \Blixt\Storage\Entities\Word  $word
     * @param int                           $frequency
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function createPresence(Field $field, Word $word, $frequency);

    /**
     * Find an occurrence by the given ID.
     *
     * @param int|string $id
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function getOccurrenceById($id);

    /**
     * Find all occurrences by the given presence.
     *
     * @param \Blixt\Storage\Entities\Occurrence $presence
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllOccurrencesByPresence(Occurrence $presence);

    /**
     * Create an occurrence record, which represents a presence and the position that it appeared in its field.
     *
     * @param \Blixt\Storage\Entities\Occurrence $presence
     * @param int                                $position
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function createOccurrence(Occurrence $presence, $position);
}