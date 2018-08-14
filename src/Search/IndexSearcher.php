<?php

namespace Blixt\Search;

use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Occurrence;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
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

        $candidateIds = $this->getCandidateDocumentIds($query);


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

        return new ResultSet();
    }

    protected function getCandidateDocumentIds(Query $query): Collection
    {
        // Get all of the clauses from the given query and key each clause by its value. We can then use this to
        // determine the required, prohibited and optional clauses.
        $clauses = $this->getClausesKeyedByValues($query);
        $requiredClauses = $this->getRequiredClauses($clauses);
//        $prohibitedClauses = $this->getProhibitedClauses($clauses);
//        $optionalClauses = $this->getOptionalClauses($clauses);

        // Load the words from storage as given by the set of clauses. If any of the required clause values are not
        // present in the set of words returned, we can't go any further so we should return an empty collection.
        $words = $this->storage->words()->getByWords($this->getClauseValues($clauses));
        if (! $this->wordsContainAllClauses($words, $requiredClauses)) {
            return Collection::make([]);
        }

        $requiredWords = $this->filterWordsForClauses($words, $requiredClauses);
//        $prohibitedWords = $this->filterWordsForClauses($words, $prohibitedClauses);
//        $optionalWords = $this->filterWordsForClauses($words, $optionalClauses);

        // Load the terms from storage by the given schema and set of words. If any of the required words are not
        // represented in the set of returned terms, we can't go any further so should return an empty collection.
        $terms = $this->storage->terms()->getBySchemaAndWords($this->schema, $words);
        if (! $this->termsContainAllWords($terms, $requiredWords)) {
            return Collection::make([]);
        }

//        $requiredTerms = $this->filterTermsForWords($terms, $requiredWords);
//        $prohibitedTerms = $this->filterTermsForWords($terms, $prohibitedWords);
//        $optionalTerms = $this->filterTermsForWords($terms, $optionalWords);

        $occurrences = $this->storage->occurrences()->getByTerms($terms);
        if ($occurrences->isEmpty()) {
            return Collection::make([]);
        }

        // There is a possibility at this point to further optimise the search process. By figuring out which fields are
        // prohibited, using the occurrences (which give us IDs of fields and terms) we've just loaded and the
        // prohibited terms we have. We can use this information to grab a smaller set of candidate document IDs by
        // immediately rejecting documents that contain fields that we've determined are prohibited. We could do this
        // either by trying to keep track of fields that contain prohibited terms and then documents that contain those
        // fields so we can remove them from the list of candidate IDs at the end or we could reduce the number of
        // documents loaded by trying to only retrieve documents that contain fields that contain optional/required
        // terms. A document that contains a prohibited term could still be loaded though, as another field belonging to
        // the document could contain no prohibited terms and would be loaded by the query that only passes
        // optional/required fields. In which case we still need to make sure that we go through an accept/reject
        // process on all of the documents before they're scored and sorted.

        // TODO - consider NOT loading field content here. They aren't needed and increase memory usage significantly.

        return $this->getDocumentIdsFromFields(
            $this->storage->fields()->getByOccurrences($occurrences)
        );
    }

    /**
     * Extract the clauses from the given query and key them by their respective values.
     *
     * @param \Blixt\Search\Query\Query $query
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getClausesKeyedByValues(Query $query)
    {
        return $query->getClauses()->keyBy(function (Clause $clause) {
            return $clause->getValue();
        });
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
     * Extract a collection of clauses that are considered optional (neither required nor prohibited) from the given set
     * of clauses.
     *
     * @param \Illuminate\Support\Collection $clauses
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getOptionalClauses(Collection $clauses): Collection
    {
        return $clauses->filter(function (Clause $clause) {
            return ! $clause->isProhibited() && ! $clause->isRequired();
        });
    }

    /**
     * Tell if the given Collection of Words contains all of the given set of Clauses.
     *
     * @param \Illuminate\Support\Collection $words
     * @param \Illuminate\Support\Collection $clauses
     *
     * @return bool
     */
    protected function wordsContainAllClauses(Collection $words, Collection $clauses): bool
    {
        // Convert the collection of Word objects to a collection of word strings for easy lookup.
        $words = $words->map(function (Word $word) {
            return $word->getWord();
        });

        // Try to find a Clause who's value is not present in $words. Finding such a Clause means that there is at least
        // one Clause that is not present in the collection of Words. Null will be returned otherwise, indicating that
        // the given collection of Words contains all the given Clauses.
        return null === $clauses->first(function (Clause $clause) use ($words) {
            return ! $words->contains($clause->getValue());
        });
    }

    /**
     * Tell if the given Collection of Terms contains all of the given set of Words.
     *
     * @param \Illuminate\Support\Collection $terms
     * @param \Illuminate\Support\Collection $words
     *
     * @return bool
     */
    protected function termsContainAllWords(Collection $terms, Collection $words): bool
    {
        // Convert the collection of Term objects into a collection of word IDs for easy lookup.
        $termWordIds = $terms->map(function (Term $term) {
            return $term->getWordId();
        });

        // Try to find a Word who's ID is not present in $termWordIds. Finding such a Word means that there is at least
        // one word that is not present in the collection of Terms. Null will be returned otherwise, indicating that the
        // given collection of Terms contains all the given Words.
        return null === $words->first(function (Word $word) use ($termWordIds) {
            return ! $termWordIds->contains($word->getId());
        });
    }

    protected function filterWordsForClauses(Collection $words, Collection $clauses): Collection
    {
        return $words->filter(function (Word $word) use ($clauses) {
            return $clauses->has($word->getWord());
        });
    }

    protected function filterTermsForWords(Collection $terms, Collection $words): Collection
    {
        return $terms->filter(function (Term $term) use ($words) {
            return $words->has($term->getWordId());
        });
    }

    protected function filterOccurrencesForTerms(Collection $occurrences, Collection $terms): Collection
    {
        return $occurrences->filter(function (Occurrence $occurrence) use ($terms) {
            return $terms->has($occurrence->getTermId());
        });
    }

    protected function getDocumentIdsFromFields(Collection $fields)
    {
        return $fields->map(function (Field $field) {
            return $field->getDocumentId();
        })->unique()->values();
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
