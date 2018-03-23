<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Term;

trait BelongsToTerm
{
    /**
     * @var int|null
     */
    protected $termId;

    /**
     * @return int|null
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * @param int|null|mixed $termId
     */
    public function setTermId($termId)
    {
        $this->termId = $termId !== null
            ? ($termId instanceof Term ? $termId->getId() : intval($termId))
            : null;
    }
}