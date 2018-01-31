<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Word;
use Blixt\Storage\Repositories\WordRepository as WordRepositoryInterface;
use Illuminate\Support\Collection;

class WordRepository extends AbstractRepository implements WordRepositoryInterface
{
    const TABLE = 'words';
    const FIELD_WORD = 'word';

    /**
     * Map an array, representing an entity into a relevant Entity object.
     *
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Word
     */
    protected function map(array $row)
    {
        return new Word(
            $row[static::FIELD_ID],
            $row[static::FIELD_WORD]
        );
    }

    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function findByWord($word)
    {
        return $this->findBy([
            static::FIELD_WORD => $word
        ]);
    }

    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function create($word)
    {
        return $this->insert([
            static::FIELD_WORD => $word
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByWords(Collection $words)
    {
        return $this->getWhere([
            static::FIELD_WORD => $words->toArray()
        ]);
    }
}