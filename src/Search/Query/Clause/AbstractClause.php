<?php

namespace Blixt\Search\Query\Clause;

abstract class AbstractClause implements Clause
{
    /**
     * @var string
     */
    protected $value;

    /**
     * BooleanClause constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get the type of the clause, which should refer to one of the above constants.
     *
     * @return int
     */
    abstract public function getType(): int;

    /**
     * Get the value of this clause. Should be a string representing a stemmed search term from the input query.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Tell if this clause represents a required term, that is a term that MUST be present in a document for it to be
     * considered a candidate.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->getType() === static::REQUIRED;
    }

    /**
     * Tell if this clause represents a prohibited term, that is a term that MUST NOT be present in a document for it to
     * be considered a candidate.
     *
     * @return bool
     */
    public function isProhibited(): bool
    {
        return $this->getType() === static::PROHIBITED;
    }
}
