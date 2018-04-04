<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Entities\Word;
use Illuminate\Support\Collection;

class TermRepository extends Repository
{
    /**
     * Find a term by the given schema and word.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param \Blixt\Persistence\Entities\Word   $word
     *
     * @return \Blixt\Persistence\Entities\Term
     */
    public function findBySchemaAndWord(Schema $schema, Word $word): Term
    {
        return $this->driver()->findBy(Term::class, [
            Term::FIELD_SCHEMA_ID => $schema->getId(),
            Term::FIELD_WORD_ID => $word->getId()
        ]);
    }

    /**
     * Find a collection of terms by the given schema and collection of words.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBySchemaAndWords(Schema $schema, Collection $words): Collection
    {
        return $this->driver()->findBy(Term::class, [
            Term::FIELD_SCHEMA_ID => $schema->getId(),
            Term::FIELD_WORD_ID => $words->map(function (Word $word) {
                return $word->getId();
            })
        ]);
    }

    /**
     * Save the given term, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Term $term
     *
     * @return \Blixt\Persistence\Entities\Term
     */
    public function save(Term $term): Term
    {
        return $term->exists() ? $this->driver()->update($term) : $this->driver()->insert($term);
    }
}