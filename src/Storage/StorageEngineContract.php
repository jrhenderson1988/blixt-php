<?php

namespace Blixt\Storage;

use Blixt\Index\Schema\Schema;

interface StorageEngineContract
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
    public function rollBackTransaction();

    /**
     * Commit the current transaction for the storage engine.
     *
     * @return bool
     */
    public function commitTransaction();


    public function findDocumentByKey($key);

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