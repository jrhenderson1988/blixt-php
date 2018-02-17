<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Entities\Term;
use Blixt\Storage\Entities\Word;
use Blixt\Storage\Repositories\TermRepository as TermRepositoryInterface;
use Illuminate\Support\Collection;

class TermRepository implements TermRepositoryInterface
{
    const TABLE = 'terms';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_WORD_ID = 'word_id';
    const FIELD_FIELD_COUNT = 'field_count';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * TermRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Blixt\Storage\Entities\Word   $word
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function findBySchemaAndWord(Schema $schema, Word $word)
    {
        $items = $this->storage->getWhere(static::TABLE, [
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_WORD_ID => $word->getId()
        ]);

        if (count($items) > 0) {
            reset($items);

            return $this->map($id = key($items), $items[$id]);
        }

        return null;
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBySchemaAndWords(Schema $schema, Collection $words)
    {
        $items = $this->storage->getWhere(static::TABLE, [
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_WORD_ID => $words->map(function (Word $word) {
                return $word->getId();
            })->toArray()
        ]);

        $results = new Collection();

        foreach ($items as $key => $item) {
            $results->put($key, $this->map($key, $item));
        }

        return $results;
    }

    /**
     * @param \Blixt\Storage\Entities\Term $term
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function save(Term $term)
    {
        return $term->exists() ? $this->update($term) : $this->create($term);
    }

    /**
     * @param \Blixt\Storage\Entities\Term $term
     *
     * @return \Blixt\Storage\Entities\Term
     */
    protected function create(Term $term)
    {
        $attributes = $this->getAttributes($term);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Term $term
     *
     * @return \Blixt\Storage\Entities\Term
     */
    protected function update(Term $term)
    {
        $attributes = $this->getAttributes($term);

        $this->storage->update(static::TABLE, $term->getId(), $attributes);

        return $term;
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Term
     */
    protected function map($key, array $row)
    {
        return new Term(
            $key,
            $row[static::FIELD_SCHEMA_ID],
            $row[static::FIELD_WORD_ID],
            $row[static::FIELD_FIELD_COUNT]
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Term $term
     *
     * @return array
     */
    protected function getAttributes(Term $term)
    {
        return [
            static::FIELD_SCHEMA_ID   => $term->getSchemaId(),
            static::FIELD_WORD_ID     => $term->getWordId(),
            static::FIELD_FIELD_COUNT => $term->getFieldCount()
        ];
    }
}