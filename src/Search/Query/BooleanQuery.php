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
        // Initialisation --
        // - Look up words related to each clause
        $words = $this->storage->words()->getByWords($this->clauses->map(function (Clause $clause) {
            return $clause->getValue();
        }));

        dd($words);
        // - Look up corresponding terms in that schema

        // Build up candidates --
        // - Look up occurrences that correspond to terms in our OR / AND clause lists
        // - Look up the fields that correspond to each of those occurrences (Only indexed fields as determine by
        //   corresponding columns)
        // - Build a set of candidate document IDs for those fields

        // Evaluation --
        // - Look up all of the documents in our set of candidate document IDs and load in ALL of their corresponding
        //   indexed fields and their occurrences
        // - Reject any documents that contain occurrences that are to be excluded (NOT)
        // - Build a set of terms that are required (AND) whether or not they appeared in the given document
        // - Reject any documents that are missing a required term
        // - What is left is our result set.
        // - Need to handle offsets and limits by ignoring documents up until the offset and stopping after the limit has
        //   been reached.

        // Performance:
        // - Consider storing reverse references to be able to look up associate items quickly
        // - Consider using a configurable cache that stores documents with all the required entities for quick lookups
        // - Consider chunking when evaluating documents, loading X at once with all the required entities in a few
        //   queries instead of doing single queries for each document.
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