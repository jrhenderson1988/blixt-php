<?php

namespace Blixt\Persistence\Entities\Concerns;

use Blixt\Persistence\Entities\Occurrence;

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
     * @param \Blixt\Persistence\Entities\Occurrence $occurrence
     */
    public function setOccurrenceIdFromOccurrence(Occurrence $occurrence): void
    {
        $this->occurrenceId = $occurrence->getId();
    }
}
