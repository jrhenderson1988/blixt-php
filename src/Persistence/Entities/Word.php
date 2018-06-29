<?php

namespace Blixt\Persistence\Entities;

class Word extends Entity
{
    /**
     * @var string
     */
    protected $word;

    /**
     * Word constructor.
     *
     * @param int|null $id
     * @param string   $word
     */
    public function __construct(?int $id, string $word)
    {
        parent::__construct($id);

        $this->setWord($word);
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @param string $word
     */
    public function setWord(string $word): void
    {
        $this->word = $word;
    }

    /**
     * Factory method to create a new Word.
     *
     * @param string $word
     *
     * @return \Blixt\Persistence\Entities\Word
     */
    public static function create(string $word): Word
    {
        return new static(null, $word);
    }

    /**
     * Factory method to make a Word from an existing record.
     *
     * @param int    $id
     * @param string $word
     *
     * @return \Blixt\Persistence\Entities\Word
     */
    public static function make(int $id, string $word): Word
    {
        return new static($id, $word);
    }
}