<?php

namespace Blixt\Search\Query;

use Blixt\Search\Query\Clauses\Clause;
use Illuminate\Support\Collection;

class BooleanQuery extends AbstractQuery implements Query
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $clauses;

    /**
     * BooleanQuery constructor.
     *
     * @param \Illuminate\Support\Collection $clauses
     */
    public function __construct(Collection $clauses)
    {
        $this->setClauses($clauses);
    }

    public function execute(): void
    {

    }

    /**
     * Add a clause to the set of clauses.
     *
     * @param \Blixt\Search\Query\Clauses\Clause $clause
     */
    public function addClause(Clause $clause): void
    {
        $this->clauses->put($clause->getIdentifier(), $clause);
    }

    /**
     * Remove the given clause from the set of clauses. If the clause was present and was removed, true is returned.
     * False will be returned otherwise.
     *
     * @param \Blixt\Search\Query\Clauses\Clause $clause
     *
     * @return bool
     */
    public function removeClause(Clause $clause): bool
    {
        return $this->removeClauseByKey($clause->getIdentifier());
    }

    /**
     * Remove a clause identified by the given key. If a matching clause was present and was removed, true is returned.
     * False will be returned otherwise.
     *
     * @param string $key
     *
     * @return bool
     */
    public function removeClauseByKey(string $key): bool
    {
        if ($this->clauses->has($key)) {
            $this->clauses->forget($key);

            return true;
        }

        return false;
    }

    /**
     * Set the clauses by running through the given collection and adding each one to the set. If anything in the
     * collection is not an instance of Clause, an exception will be thrown by PHP.
     *
     * @param \Illuminate\Support\Collection $clauses
     */
    public function setClauses(Collection $clauses): void
    {
        $this->clauses = Collection::make();

        $clauses->each(function (Clause $clause) {
            $this->addClause($clause);
        });
    }

    public static function parse(string $query): BooleanQuery
    {

    }
}