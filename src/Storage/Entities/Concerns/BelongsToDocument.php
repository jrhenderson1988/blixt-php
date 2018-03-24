<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Document;

trait BelongsToDocument
{
    /**
     * @var int
     */
    protected $documentId;

    /**
     * @return int
     */
    public function getDocumentId(): int
    {
        return $this->documentId;
    }

    /**
     * @param int $documentId
     */
    public function setDocumentId(int $documentId): void
    {
        $this->documentId = $documentId;
    }

    /**
     * @param \Blixt\Storage\Entities\Document $document
     */
    public function setDocumentIdFromDocument(Document $document): void
    {
        $this->documentId = $document->getId();
    }
}