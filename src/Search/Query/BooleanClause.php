<?php

namespace Blixt\Search\Query;

use InvalidArgumentException;

class BooleanClause
{
    public const SHOULD = 1;
    public const REQUIRED = 2;
    public const PROHIBITED = 3;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $value;

    /**
     * BooleanClause constructor.
     *
     * @param int $type
     * @param string $value
     */
    public function __construct(int $type, string $value)
    {
        if (! in_array($type, [static::SHOULD, static::REQUIRED, static::PROHIBITED])) {
            throw new InvalidArgumentException('Invalid type provided.');
        }

        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->type === static::REQUIRED;
    }

    /**
     * Tell if this clause is considered prohibited, that is, documents that contain this term are immediately rejected.
     *
     * @return bool
     */
    public function isProhibited(): bool
    {
        return $this->type === static::PROHIBITED;
    }
}
