<?php

namespace Blixt\Storage\Entities\Concerns;

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
        $this->termId = $termId !== null ? intval($termId) : null;
    }

    /**
     * Fluent getter/setter for termId.
     *
     * @param int|null|mixed $termId
     *
     * @return $this|int|null
     */
    public function termId($termId = null)
    {
        if (func_num_args() === 0) {
            return $this->getTermId();
        }

        $this->setTermId($termId);

        return $this;
    }
}