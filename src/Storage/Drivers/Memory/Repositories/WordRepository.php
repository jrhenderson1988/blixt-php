<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Word;
use Blixt\Storage\Repositories\WordRepository as WordRepositoryInterface;

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
}