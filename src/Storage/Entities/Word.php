<?php

namespace Blixt\Storage\Entities;

class Word extends Entity
{
    /**
     * @var string|null
     */
    protected $word;

    /**
     * Word constructor.
     *
     * @param $id
     * @param $word
     */
    public function __construct($id, $word)
    {
        parent::__construct($id);

        $this->setWord($word);
    }

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