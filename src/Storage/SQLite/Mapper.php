<?php

namespace Blixt\Storage\SQLite;

use Blixt\Models\Document;

class Mapper
{
    /**
     * Map the given array or object into a Document model instance.
     *
     * @param array|\stdClass $document
     *
     * @return \Blixt\Models\Document|null
     */
    public function document($document)
    {
        if (is_array($document)) {
            return new Document(
                $document['id'], $document['key']
            );
        } elseif ($document instanceof \stdClass) {
            return new Document(
                $document->id, $document->key
            );
        }

        return null;
    }
}