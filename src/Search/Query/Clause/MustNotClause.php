<?php

namespace Blixt\Search\Query\Clause;

class MustNotClause extends AbstractClause
{
    /**
     * Create a MustNotClause for the given value.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    /**
     * Get the type of the clause, which should refer to one of the above constants.
     *
     * @return int
     */
    public function getType(): int
    {
        return Clause::PROHIBITED;
    }
}
