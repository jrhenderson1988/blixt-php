<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Entities\Term;
use Blixt\Storage\Entities\Word;
use Blixt\Storage\Repositories\TermRepository as TermRepositoryInterface;
use Illuminate\Support\Collection;

class TermRepository extends AbstractRepository implements TermRepositoryInterface
{
    const ENTITY = Term::class;
    const TABLE = 'terms';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_WORD_ID = 'word_id';
    const FIELD_FIELD_COUNT = 'field_count';

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Blixt\Storage\Entities\Word   $word
     *
     * @return \Blixt\Storage\Entities\Term
     * @throws \Blixt\Exceptions\StorageException
     */
    public function findBySchemaAndWord(Schema $schema, Word $word)
    {
        return $this->findEntityBy([
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_WORD_ID => $word->getId()
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     * @throws \Blixt\Exceptions\StorageException
     */
    public function getBySchemaAndWords(Schema $schema, Collection $words)
    {
        return $this->getEntitiesBy([
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_WORD_ID => $words->map(function (Word $word) {
                return $word->getId();
            })->toArray()
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Term $term
     *
     * @return \Blixt\Storage\Entities\Term
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Term $term)
    {
        return $this->saveEntity($term);
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
     * @param \Blixt\Storage\Entities\Entity $entity
     *
     * @return array
     */
    protected function getAttributes(Entity $entity)
    {
        return [
            static::FIELD_SCHEMA_ID   => $entity->getSchemaId(),
            static::FIELD_WORD_ID     => $entity->getWordId(),
            static::FIELD_FIELD_COUNT => $entity->getFieldCount()
        ];
    }
}