<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Word;
use Illuminate\Support\Collection;

class WordRepository extends Repository
{
    /**
     * Find a Word entity by the given word string.
     *
     * @param string $word
     *
     * @return \Blixt\Persistence\Entities\Word|null
     */
    public function findByWord(string $word): ?Word
    {
        return $this->driver()->findBy(Word::class, [
            Word::FIELD_WORD => $word
        ]);
    }

    /**
     * Get a collection of Word entities by the given collection of word strings.
     *
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByWords(Collection $words): Collection
    {
        return $this->driver()->getWhere(Word::class, [
            Word::FIELD_WORD => $words
        ]);
    }

    /**
     * Save the given word, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Word $word
     *
     * @return \Blixt\Persistence\Entities\Word
     */
    public function save(Word $word): Word
    {
        return $word->exists() ? $this->driver()->update($word) : $this->driver()->insert($word);
    }
}