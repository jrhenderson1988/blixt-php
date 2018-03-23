<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Word;

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
        $this->wordId = $wordId !== null
            ? ($wordId instanceof Word ? $wordId->getId() : intval($wordId))
            : null;
    }
}