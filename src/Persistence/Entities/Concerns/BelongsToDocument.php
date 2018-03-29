<?php

namespace Blixt\Persistence\Entities\Concerns;

use Blixt\Persistence\Entities\Document;

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
     * @param \Blixt\Persistence\Entities\Document $document
     */
    public function setDocumentIdFromDocument(Document $document): void
    {
        $this->documentId = $document->getId();
    }
}