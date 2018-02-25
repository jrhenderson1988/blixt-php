<?php

namespace Blixt\Storage\Entities\Concerns;

trait BelongsToWord
{
    /**
     * @var int|null
     */
    protected $wordId;

    /**
     * @return int|null|mixed
     */
    public function getWordId()
    {
        return $this->wordId;
    }

    /**
     * @param int|null|mixed $wordId
     */
    public function setWordId($wordId)
    {
        $this->wordId = $wordId !== null ? intval($wordId) : null;
    }

    /**
     * Fluent getter/setter for wordId.
     *
     * @param int|null|mixed $wordId
     *
     * @return $this|int|mixed|null
     */
    public function wordId($wordId = null)
    {
        if (func_num_args() === 0) {
            return $this->getWordId();
        }

        $this->setWordId($wordId);

        return $this;
    }
}