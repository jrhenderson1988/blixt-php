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
     * @var string
     */
    protected $prefix;

    /**
     * Token constructor.
     *
     * @param string $text
     * @param int $position
     * @param string $prefix
     */
    public function __construct(string $text, int $position, string $prefix = '')
    {
        $this->setText($text);
        $this->setPosition($position);
        $this->setPrefix($prefix);
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

    /**
     * Get the prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Set the prefix.
     *
     * @param string $prefix
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }
}
