<?php

namespace Blixt\Persistence\Entities;

class Word extends Entity
{
    const TABLE = 'words';
    const FIELD_WORD = 'word';

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
     * Convert this entity to an array for storage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            static::FIELD_ID => $this->getId(),
            static::FIELD_WORD=> $this->getWord()
        ];
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

    /**
     * Create a Word from the given array.
     *
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Word
     */
    public static function fromArray(array $attributes): Word
    {
        return new static(
            $attributes[static::FIELD_ID],
            $attributes[static::FIELD_WORD]
        );
    }
}