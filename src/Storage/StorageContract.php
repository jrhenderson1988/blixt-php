<?php

namespace Blixt\Storage;

use Blixt\Index\Schema\Schema;
use Blixt\Models\Column;
use Blixt\Models\Document;
use Blixt\Models\Field;

interface StorageContract
{
    /**
     * Get the name of the schema represented by the storage engine.
     *
     * @return string
     */
    public function getName();

    /**
     * Tell if the storage represented by the engine exists.
     *
     * @return boolean
     */
    public function exists();

    /**
     * Create the storage represented by the engine.
     *
     * @param \Blixt\Index\Schema\Schema $schema
     *
     * @return bool
     */
    public function create(Schema $schema);

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

    /**
     * Load all of the columns from the storage as a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getColumns();

    /**
     * Find a document in the storage by the given key. If no such document exists, null should be returned.
     *
     * @param mixed $key
     *
     * @return \Blixt\Models\Document|null
     */
    public function getDocumentByKey($key);

    /**
     * Create a document in the storage with the given key.
     *
     * @param mixed $key
     *
     * @return \Blixt\Models\Document
     */
    public function createDocument($key);

    /**
     * Create a field for the given document and column, with the given value.
     *
     * @param \Blixt\Models\Document $document
     * @param \Blixt\Models\Column   $column
     * @param mixed|null             $value
     *
     * @return \Blixt\Models\Field
     */
    public function createField(Document $document, Column $column, $value = null);

    /**
     * Find a word by the given word string.
     *
     * @param string $word
     *
     * @return \Blixt\Models\Word|null
     */
    public function getWordByWord($word);

    /**
     * Create a word, given a word string.
     *
     * @param string $word
     *
     * @return \Blixt\Models\Word
     */
    public function createWord($word);

//    public function findTermByName($name);
//    public function findTermsByName(Collection $names);
//    public function createTerm($name);
//    public function createTerms(Collection $names);
//    public function findSchemaByName($name);
//    public function createSchema($name);
//    public function findColumn($schemaId, $name);
//    public function findColumns($schemaId, Collection $names);
//    public function createColumn($schemaId, $name);
//    public function createColumns($schemaId, Collection $names);
//    public function findDocument($schemaId, $primaryKey);
//    public function findDocuments($schemaId, Collection $primaryKeys);
//    public function createDocument($schemaId, $primaryKey);
//    public function createDocuments($schemaId, Collection $primaryKeys);
//    public function findAttribute($documentId, $columnId);
//    public function findAttributes($documentId, Collection $columnIds);
//    public function createAttribute($documentId, $columnId, $value);
//    public function createAttributes($documentId, $columnId, $value);
}