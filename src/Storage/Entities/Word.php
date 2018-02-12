<?php

namespace Blixt\Storage\Entities;

class Word extends Entity
{
    /**
     * @var string|null
     */
    protected $word;

    /**
     * @return string|null
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param string|null|mixed $word
     */
    public function setWord($word)
    {
        $this->word = $word !== null ? strval($word) : null;
    }
}