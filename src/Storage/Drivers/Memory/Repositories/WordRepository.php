<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Word;
use Blixt\Storage\Repositories\WordRepository as WordRepositoryInterface;
use Illuminate\Support\Collection;

class WordRepository implements WordRepositoryInterface
{
    const TABLE = 'words';
    const FIELD_WORD = 'word';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * WordRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function findByWord($word)
    {
        $items = $this->storage->getWhere(static::TABLE, [
            static::FIELD_WORD => $word
        ]);

        if (count($items) > 0) {
            reset($items);

            return $this->map($id = key($items), $items[$id]);
        }

        return null;
    }

    /**
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByWords(Collection $words)
    {
        $items = $this->storage->getWhere(static::TABLE, [
            static::FIELD_WORD => $words->toArray()
        ]);

        $results = new Collection();

        foreach ($items as $key => $item) {
            $results->put($key, $this->map($key, $item));
        }

        return $results;
    }

    /**
     * @param \Blixt\Storage\Entities\Word $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function save(Word $word)
    {
        return $word->exists() ? $this->update($word) : $this->create($word);
    }

    /**
     * @param \Blixt\Storage\Entities\Word $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    protected function create(Word $word)
    {
        $attributes = $this->getAttributes($word);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Word $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    protected function update(Word $word)
    {
        $attributes = $this->getAttributes($word);

        $this->storage->update(static::TABLE, $word->getId(), $attributes);

        return $word;
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
     * @param \Blixt\Storage\Entities\Word $word
     *
     * @return array
     */
    protected function getAttributes(Word $word)
    {
        return [
            static::FIELD_WORD => $word->getWord()
        ];
    }
}