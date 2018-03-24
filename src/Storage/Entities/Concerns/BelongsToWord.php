<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Word;

trait BelongsToWord
{
    /**
     * @var int
     */
    protected $wordId;

    /**
     * @return int
     */
    public function getWordId(): int
    {
        return $this->wordId;
    }

    /**
     * @param int $wordId
     */
    public function setWordId(int $wordId): void
    {
        $this->wordId = $wordId;
    }

    /**
     * @param \Blixt\Storage\Entities\Word $word
     */
    public function setWordIdFromWord(Word $word): void
    {
        $this->wordId = $word->getId();
    }
}