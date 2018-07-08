<?php

namespace Blixt\Search\Query;

use Blixt\Search\Query\Scorer\Scorer;
use Illuminate\Support\Collection;

abstract class AbstractQuery implements Query
{
    /**
     * @var \Blixt\Search\Query\Scorer\Scorer
     */
    protected $scorer;

    /**
     * Get the query's scorer to allow us to calculate a score for each candidate document. Calls the abstract
     * createScorer() method to create an instance of the relevant scorer and caches it in the $scorer property.
     *
     * @return \Blixt\Search\Query\Scorer\Scorer
     */
    public function getScorer(): Scorer
    {
        if (! $this->scorer) {
            $this->scorer = $this->createScorer();
        }

        return $this->scorer;
    }

    /**
     * Create a relevant scorer for the query.
     *
     * @return \Blixt\Search\Query\Scorer\Scorer
     */
    abstract protected function createScorer(): Scorer;

    /**
     * Get the collection of clauses that form the query.
     *
     * @return \Illuminate\Support\Collection
     */
    abstract public function getClauses(): Collection;

    /**
     * Implement the logic required to get a list of identifiers for documents that are considered candidates and will
     * be subsequently scored in order to build up a set of search results.
     *
     * @return \Illuminate\Support\Collection
     */
    abstract public function getCandidateDocumentIds(): Collection;
}