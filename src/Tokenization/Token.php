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
    public function __construct($text, $position = 0)
    {
        $this->setText($text);
        $this->setPosition($position);
    }

    /**
     * Get the text.
     *
     * @return string
     */
    public function getText()
    {
        return strval($this->text);
    }

    /**
     * Set the text.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = strval($text);
    }

    /**
     * Get the position.
     *
     * @return int
     */
    public function getPosition()
    {
        return intval($this->position);
    }

    /**
     * Set the position.
     *
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = intval($position);
    }
}