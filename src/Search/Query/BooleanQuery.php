<?php

namespace Blixt\Search\Query;

use Blixt\Search\Query\Scorer\BooleanScorer;
use Blixt\Search\Query\Scorer\Scorer;
use Illuminate\Support\Collection;

// Initialisation --
// - Look up words related to each clause
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
class BooleanQuery extends AbstractQuery implements Query
{
    /**
     * Create a relevant scorer for the query.
     *
     * @return \Blixt\Search\Query\Scorer\Scorer
     */
    protected function createScorer(): Scorer
    {
        return new BooleanScorer();
    }

    /**
     * Implement the logic required to get a list of identifiers for documents that are considered candidates and will
     * be subsequently scored in order to build up a set of search results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCandidateDocumentIds(): Collection
    {
        // TODO: Implement getCandidateDocumentIds() method.
        return null;
    }
}
