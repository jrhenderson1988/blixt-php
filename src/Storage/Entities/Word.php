<?php

namespace Blixt\Storage\Entities;

class Word extends Entity
{
    /**
     * @var string
     */
    protected $word;

    /**
     * Word constructor.
     *
     * @param int|mixed    $id
     * @param string|mixed $word
     */
    public function __construct($id, $word)
    {
        parent::__construct($id);

        $this->setWord($word);
    }

    /**
     * Get the word.
     *
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Set the word.
     *
     * @param string|mixed $word
     */
    public function setWord($word)
    {
        $this->word = strval($word);
    }
}