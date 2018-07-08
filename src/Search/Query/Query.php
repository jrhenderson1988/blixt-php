<?php

namespace Blixt\Search\Query;

use Blixt\Search\Query\Scorer\Scorer;
use Illuminate\Support\Collection;

interface Query
{
    /**
     * Get the query's scorer to allow us to calculate a score for each candidate document.
     *
     * @return \Blixt\Search\Query\Scorer\Scorer
     */
    public function getScorer(): Scorer;

    /**
     * Get the collection of clauses that form the query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClauses(): Collection;

    /**
     * Implement the logic required to get a list of identifiers for documents that are considered candidates and will
     * be subsequently scored in order to build up a set of search results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCandidateDocumentIds(): Collection;
}
