<?php

namespace Blixt\Search\Query\Clauses;

interface Clause
{
    /**
     * Get an identifier for this clause, which should include the type of clause and the value that it refers to. This
     * is used to avoid adding duplicate clauses to a query.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Get the value to which this clause refers.
     *
     * @return string
     */
    public function getValue(): string;
}
