<?php

namespace Blixt\Storage;

use Blixt\Documents\Document;
use Illuminate\Support\Collection;

interface EngineInterface
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
     * @return boolean
     */
    public function create();

    /**
     * Destroy the storage represented by the engine.
     *
     * @return boolean
     */
    public function destroy();

//    public function addDocument(Document $document);
//    public function search();

    /**
     * Begin a transaction for the storage engine.
     *
     * @return bool
     */
    public function beginTransaction();

    /**
     * Roll back the current transaction for the storage engine.
     *
     * @return bool
     */
    public function rollbackTransaction();

    /**
     * Commit the current transaction for the storage engine.
     *
     * @return bool
     */
    public function commitTransaction();

    /**
     * Find a term by its name.
     *
     * @param string $name
     *
     * @return \Blixt\Models\Term
     */
    public function findTermByName($name);

    /**
     * Find a collection of terms by their given names.
     *
     * @param \Illuminate\Support\Collection $names
     *
     * @return \Illuminate\Support\Collection
     */
    public function findTermsByName(Collection $names);

    /**
     * Create a new term with the given name.
     *
     * @param string $name
     *
     * @return \Blixt\Models\Term
     */
    public function createTerm($name);

    /**
     * Create a collection of terms with the given set of names.
     *
     * @param \Illuminate\Support\Collection $names
     *
     * @return \Illuminate\Support\Collection
     */
    public function createTerms(Collection $names);

    /**
     * Find a schema by its name.
     *
     * @param string $name
     *
     * @return \Blixt\Models\Schema
     */
    public function findSchemaByName($name);

    /**
     * Create a new schema with the given name.
     *
     * @param string $name
     *
     * @return \Blixt\Models\Schema
     */
    public function createSchema($name);
    public function findColumn($schemaId, $name);
    public function findColumns($schemaId, Collection $names);
    public function createColumn($schemaId, $name);
    public function createColumns($schemaId, Collection $names);
    public function findDocument($schemaId, $primaryKey);
    public function findDocuments($schemaId, Collection $primaryKeys);
    public function createDocument($schemaId, $primaryKey);
    public function createDocuments($schemaId, Collection $primaryKeys);
    public function findAttribute($documentId, $columnId);
    public function findAttributes($documentId, Collection $columnIds);
    public function createAttribute($documentId, $columnId, $value);
    public function createAttributes($documentId, $columnId, $value);
}