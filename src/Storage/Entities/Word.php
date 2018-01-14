<?php

namespace Blixt\Storage\Entities;

/**
 * @Entity
 * @Table(
 *     name="words",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="uq_words_word", columns={"word"})
 *     }
 * )
 */
class Word
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $word;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param string|mixed $word
     */
    public function setWord($word)
    {
        $this->word = strval($word);
    }
}