<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Entities\Term;
use Blixt\Storage\Entities\Word;
use Blixt\Storage\Repositories\TermRepository as TermRepositoryInterface;
use Illuminate\Support\Collection;

class TermRepository extends AbstractRepository implements TermRepositoryInterface
{
    const TABLE = 'terms';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_WORD_ID = 'word_id';
    const FIELD_DOCUMENT_COUNT = 'document_count';

    /**
     * Map an array, representing an entity into a relevant Entity object.
     *
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Term
     */
    protected function map(array $row)
    {
        return new Term(
            $row[static::FIELD_ID],
            $row[static::FIELD_SCHEMA_ID],
            $row[static::FIELD_WORD_ID],
            $row[static::FIELD_DOCUMENT_COUNT]
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Blixt\Storage\Entities\Word   $word
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function findBySchemaAndWord(Schema $schema, Word $word)
    {
        return $this->findBy([
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_WORD_ID => $word->getId(),
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBySchemaAndWords(Schema $schema, Collection $words)
    {
        return $this->getWhere([
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_WORD_ID => $words->map(function (Word $word) {
                return $word->getId();
            })->toArray()
        ]);
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Blixt\Storage\Entities\Word   $word
     * @param int|mixed                      $fieldCount
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function create(Schema $schema, Word $word, $fieldCount = 0)
    {
        return $this->insert([
            static::FIELD_SCHEMA_ID => $schema->getId(),
            static::FIELD_WORD_ID => $word->getId(),
            static::FIELD_DOCUMENT_COUNT => intval($fieldCount),
        ]);
    }
}