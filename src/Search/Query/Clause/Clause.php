<?php

namespace Blixt\Search\Query\Clause;

interface Clause
{
    public const SHOULD = 1;
    public const REQUIRED = 2;
    public const PROHIBITED = 3;

    /**
     * Get the type of the clause, which should refer to one of the above constants.
     *
     * @return int
     */
    public function getType(): int;

    /**
     * Get the value of this clause. Should be a string representing a stemmed search term from the input query.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Tell if this clause represents a required term, that is a term that MUST be present in a document for it to be
     * considered a candidate.
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * Tell if this clause represents a prohibited term, that is a term that MUST NOT be present in a document for it to
     * be considered a candidate.
     *
     * @return bool
     */
    public function isProhibited(): bool;
}