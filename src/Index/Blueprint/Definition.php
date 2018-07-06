<?php

namespace Blixt\Index\Blueprint;

class Definition
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $indexed;

    /**
     * @var bool
     */
    protected $stored;

    /**
     * Definition constructor.
     *
     * @param string $name
     * @param bool   $indexed
     * @param bool   $stored
     */
    public function __construct(string $name, bool $indexed = true, bool $stored = false)
    {
        $this->name = $name;
        $this->indexed = $indexed;
        $this->stored = $stored;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isIndexed(): bool
    {
        return $this->indexed;
    }

    /**
     * @return bool
     */
    public function isStored(): bool
    {
        return $this->stored;
    }
}
