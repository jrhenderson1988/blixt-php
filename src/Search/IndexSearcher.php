<?php

namespace Blixt\Search;

use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Word;
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

        dd($this->getCandidateDocumentIds($query));


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

    protected function getCandidateDocumentIds(Query $query): Collection
    {
        $clauses = $query->getClauses();

        $requiredClauses = $this->getRequiredClauses($clauses);

        // Load the words from storage as given by the set of clauses. If any of the required clause values are not
        // present in the set of words returned, we can't go any further so we should return an empty collection.
        $words = $this->storage->words()->getByWords($this->getClauseValues($clauses));
        if (! $this->wordsContainAllRequiredClauses($words, $requiredClauses)) {
            return Collection::make([]);
        }

        // Load the terms from storage by the given schema and set of words. If any of the required words are not
        // represented in the set of returned terms, we can't go any further so should return an empty collection.
        $terms = $this->storage->terms()->getBySchemaAndWords($this->schema, $words);

        dd($words, $clauses);







//        $prohibitedWordIds = collect([]);
//        $requiredWordIds = collect([]);
//
//        $words = $this->getMatchingWords($query->getClauses());
//        $words->each(function (Word $word) {
//
//        });
//
//        $terms = $this->getTermsFromWords($words);
//
//        $occurrences = $this->getOccurrencesFromTerms($terms);
//        // TODO - Filter out occurrences that
//        $fields = $this->getFieldsFromOccurrences($occurrences);
//
//        return $fields->map(function (Field $field) {
//            return $field->getDocumentId();
//        })->unique();
    }

    /**
     * Get a collection of all the values of the given clauses, i.e. the word strings they represent.
     *
     * @param \Illuminate\Support\Collection $clauses
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getClauseValues(Collection $clauses): Collection
    {
        return $clauses->map(function (Clause $clause) {
            return $clause->getValue();
        });
    }

    /**
     * Extract a collection of clauses that are considered prohibited from the given set of clauses.
     *
     * @param \Illuminate\Support\Collection $clauses
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getProhibitedClauses(Collection $clauses): Collection
    {
        return $clauses->filter(function (Clause $clause) {
            return $clause->isProhibited();
        });
    }

    /**
     * Extract a collection of clauses that are considered required from the given set of clauses.
     *
     * @param \Illuminate\Support\Collection $clauses
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getRequiredClauses(Collection $clauses): Collection
    {
        return $clauses->filter(function (Clause $clause) {
            return $clause->isRequired();
        });
    }

    /**
     * Tell if the given Collection of Words contains all of the required clauses present in the given set.
     *
     * @param \Illuminate\Support\Collection $words
     * @param \Illuminate\Support\Collection $clauses
     *
     * @return bool
     */
    protected function wordsContainAllRequiredClauses(Collection $words, Collection $clauses): bool
    {
        // Convert the collection of Word objects to a collection of word strings for easy lookup.
        $words = $words->map(function (Word $word) {
            return $word->getWord();
        });

        // The idea here is to find a required clause that is missing from the collection of words. If we find such a
        // clause it is returned. If $missing is null, then it means we were unable to find a clause that was missing a
        // required word, so we can be sure that
        return $clauses->first(function (Clause $clause) use ($words) {
            return $clause->isRequired() && ! $words->contains($clause->getValue());
        }) === null;
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
