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
     * @param int|null|mixed $id
     * @param int|null|mixed $word
     */
    public function __construct($id = null, $word = null)
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

    /**
     * Fluent getter/setter for word.
     *
     * @param string|null|mixed $word
     *
     * @return $this|null|string
     */
    public function word($word = null)
    {
        if (func_num_args() === 0) {
            return $this->getWord();
        }

        $this->setWord($word);

        return $this;
    }
}