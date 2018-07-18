<?php

namespace Blixt\Search;

use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\StorageManager;
use Blixt\Search\Query\Clause\Clause;
use Blixt\Search\Query\Query;
use Blixt\Search\Results\ResultSet;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;

class IndexSearcher
{
    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $schema;

    /**
     * @var \Blixt\Persistence\StorageManager
     */
    protected $storage;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * IndexSearcher constructor.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param \Blixt\Persistence\StorageManager $storage
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function __construct(Schema $schema, StorageManager $storage, Tokenizer $tokenizer)
    {
        $this->schema = $schema;
        $this->storage = $storage;
        $this->tokenizer = $tokenizer;
    }

    public function search(Query $query): ResultSet
    {
        // TODO
        // - Change all of the fancy query stuff to just be a DefaultQuery. Query should act as a DTO for the clauses.
        // - IndexSearcher carries out all the logic for searching, filtering and using scorer to score each document.
        // - A scorer class is responsible for scoring each document and different scorers can alter the ordering of
        //   results and how each document is placed.
        $words = $this->getMatchingWords($query->getClauses());
        $terms = $this->getTermsFromWords($words);
        $occurrences = $this->getOccurrencesFromTerms($terms);
        dd($occurrences);


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
    }

    protected function getMatchingWords(Collection $clauses)
    {
        return $this->storage->words()->getByWords($clauses->map(function (Clause $clause) {
            return $clause->getValue();
        }));
    }

    protected function getTermsFromWords(Collection $words)
    {
        return $this->storage->terms()->getBySchemaAndWords($this->schema, $words);
    }

    protected function getOccurrencesFromTerms(Collection $terms)
    {
        return $this->storage->occurrences()->getByTerms($terms);
    }

    protected function getFieldsFromOccurrences(Collection $occurrences)
    {
        return $this->storage->fields()->getByOccurrences($occurrences);
    }
}
