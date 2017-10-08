<?php

namespace Blixt\Storage;

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
     */
    public function create();

    /**
     * Destroy the storage represented by the engine.
     */
    public function destroy();
}