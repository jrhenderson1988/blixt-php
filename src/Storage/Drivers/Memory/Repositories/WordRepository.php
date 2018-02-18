<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Entities\Word;
use Blixt\Storage\Repositories\WordRepository as WordRepositoryInterface;
use Illuminate\Support\Collection;

class WordRepository extends AbstractRepository implements WordRepositoryInterface
{
    const ENTITY = Word::class;
    const TABLE = 'words';
    const FIELD_WORD = 'word';

    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     * @throws \Blixt\Exceptions\StorageException
     */
    public function findByWord($word)
    {
        return $this->findEntityBy([
            static::FIELD_WORD => $word
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     * @throws \Blixt\Exceptions\StorageException
     */
    public function getByWords(Collection $words)
    {
        return $this->getEntitiesBy([
            static::FIELD_WORD => $words->toArray()
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Word $word
     *
     * @return \Blixt\Storage\Entities\Word
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Word $word)
    {
        return $this->saveEntity($word);
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Word
     */
    protected function map($key, array $row)
    {
        return new Word(
            $key,
            $row[static::FIELD_WORD]
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return array
     */
    protected function getAttributes(Entity $entity)
    {
        return [
            static::FIELD_WORD => $entity->getWord()
        ];
    }
}