<?php

namespace Blixt\Tokenization;

class Token
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $position;

    /**
     * Token constructor.
     *
     * @param string $text
     * @param int    $position
     */
    public function __construct(string $text, int $position = 0)
    {
        $this->setText($text);
        $this->setPosition($position);
    }

    /**
     * Get the text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set the text.
     *
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * Get the position.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set the position.
     *
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}