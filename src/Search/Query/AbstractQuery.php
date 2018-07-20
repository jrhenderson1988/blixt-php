<?php

namespace Blixt\Search\Query;

use Blixt\Search\Query\Clause\Clause;
use Illuminate\Support\Collection;

abstract class AbstractQuery implements Query
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $clauses;

    /**
     * AbstractQuery constructor.
     *
     * @param \Illuminate\Support\Collection $clauses
     */
    public function __construct(Collection $clauses)
    {
        $this->setClauses($clauses);
    }

    /**
     * Get the collection of clauses that form the query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClauses(): Collection
    {
        return $this->clauses;
    }

    /**
     * Set the clauses for this query.
     *
     * @param \Illuminate\Support\Collection $clauses
     */
    public function setClauses(Collection $clauses): void
    {
        $this->clauses = $clauses->map(function (Clause $clause) {
            return $clause;
        })->keyBy(function (Clause $clause) {
            return $clause->getValue();
        });
    }
}
