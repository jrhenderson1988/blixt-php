<?php

namespace Blixt\Storage;

use Blixt\Documents\Document;

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

    /**
     * Add a document to the index.
     *
     * @param \Blixt\Documents\Document $document
     *
     * @return boolean
     */
    public function addDocument(Document $document);

//    public function search();
}