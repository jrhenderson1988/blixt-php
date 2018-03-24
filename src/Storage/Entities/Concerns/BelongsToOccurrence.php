<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Occurrence;

trait BelongsToOccurrence
{
    /**
     * @var int
     */
    protected $occurrenceId;

    /**
     * @return int
     */
    public function getOccurrenceId(): int
    {
        return $this->occurrenceId;
    }

    /**
     * @param int $occurrenceId
     */
    public function setOccurrenceId(int $occurrenceId): void
    {
        $this->occurrenceId = $occurrenceId;
    }

    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     */
    public function setOccurrenceIdFromOccurrence(Occurrence $occurrence): void
    {
        $this->occurrenceId = $occurrence->getId();
    }
}