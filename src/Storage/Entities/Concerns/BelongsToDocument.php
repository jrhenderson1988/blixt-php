<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Document;

trait BelongsToDocument
{
    /**
     * @var int|null
     */
    protected $documentId;

    /**
     * @return int|null
     */
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param int|null|mixed $documentId
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId !== null
            ? ($documentId instanceof Document ? $documentId->getId() : intval($documentId))
            : null;
    }
}