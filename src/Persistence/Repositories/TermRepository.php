<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Entities\Word;
use Illuminate\Support\Collection;

/**
 * TermRepository.
 *
 * @method Collection getWhere(array $conditions, int $offset = 0, int $limit = null)
 * @method Collection all(int $offset = 0, int $limit = null)
 * @method Term|null findBy(array $conditions)
 * @method Term|null find(int $id)
 * @method Term create(Term $entity)
 * @method Term update(Term $entity)
 * @method Term save(Term $entity)
 *
 * @package Blixt\Persistence\Repositories
 */
class TermRepository extends Repository
{
    public const TABLE = 'terms';
    public const SCHEMA_ID = 'schema_id';
    public const WORD_ID = 'word_id';
    public const FIELD_COUNT = 'field_count';

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Term|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function toAttributes(Entity $entity): array
    {
        return [
            static::SCHEMA_ID => $entity->getSchemaId(),
            static::WORD_ID => $entity->getWordId(),
            static::FIELD_COUNT => $entity->getFieldCount()
        ];
    }

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Term|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return Term::make(
            $id,
            $attributes[static::SCHEMA_ID],
            $attributes[static::WORD_ID],
            $attributes[static::FIELD_COUNT]
        );
    }

    /**
     * Find a term by the given schema and word.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param \Blixt\Persistence\Entities\Word   $word
     *
     * @return \Blixt\Persistence\Entities\Term|\Blixt\Persistence\Entities\Entity|null
     */
    public function findBySchemaAndWord(Schema $schema, Word $word): ?Term
    {
        return $this->findBy([
            static::SCHEMA_ID => $schema->getId(),
            static::WORD_ID => $word->getId()
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
        return $this->getWhere([
            static::SCHEMA_ID => $schema->getId(),
            static::WORD_ID => $words->map(function (Word $word) {
                return $word->getId();
            })->all()
        ]);
    }
}