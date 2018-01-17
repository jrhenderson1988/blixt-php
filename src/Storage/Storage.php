<?php

namespace Blixt\Storage;

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
     * Get (or create) the column repository.
     *
     * @return \Blixt\Storage\Repositories\ColumnRepository
     */
    public function columnRepository();

    /**
     * Get (or create) the document repository.
     *
     * @return \Blixt\Storage\Repositories\DocumentRepository
     */
    public function documentRepository();

    /**
     * Get (or create) the field repository.
     *
     * @return \Blixt\Storage\Repositories\FieldRepository
     */
    public function fieldRepository();

    /**
     * Get (or create) the occurrence repository.
     *
     * @return \Blixt\Storage\Repositories\OccurrenceRepository
     */
    public function occurrenceRepository();

    /**
     * Get (or create) the position repository.
     *
     * @return \Blixt\Storage\Repositories\PositionRepository
     */
    public function positionRepository();

    /**
     * Get (or create) the schema repository.
     *
     * @return \Blixt\Storage\Repositories\SchemaRepository
     */
    public function schemaRepository();

    /**
     * Get (or create) the term repository.
     *
     * @return \Blixt\Storage\Repositories\TermRepository
     */
    public function termRepository();

    /**
     * Get (or create) the word repository.
     *
     * @return \Blixt\Storage\Repositories\WordRepository
     */
    public function wordRepository();
}