<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Term;

trait BelongsToTerm
{
    /**
     * @var int
     */
    protected $termId;

    /**
     * @return int
     */
    public function getTermId(): int
    {
        return $this->termId;
    }

    /**
     * @param int $termId
     */
    public function setTermId(int $termId): void
    {
        $this->termId = $termId;
    }

    /**
     * @param \Blixt\Storage\Entities\Term $term
     */
    public function setTermIdFromTerm(Term $term): void
    {
        $this->termId = $term->getId();
    }
}