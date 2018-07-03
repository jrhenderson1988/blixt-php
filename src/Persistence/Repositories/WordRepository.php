<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Entities\Word;
use Illuminate\Support\Collection;

/**
 * WordRepository.
 *
 * @method Collection getWhere(array $conditions, int $offset = 0, int $limit = null)
 * @method Collection all(int $offset = 0, int $limit = null)
 * @method Word|null findBy(array $conditions)
 * @method Word|null find(int $id)
 * @method Word create(Word $entity)
 * @method Word update(Word $entity)
 * @method Word save(Word $entity)
 *
 * @package Blixt\Persistence\Repositories
 */
class WordRepository extends Repository
{
    public const TABLE = 'words';
    public const WORD = 'word';

    /**
     * Get the attributes from the given entity.
     *
     * @param \Blixt\Persistence\Entities\Word|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function getAttributes(Entity $entity): array
    {
        return [
            static::WORD => $entity->getWord()
        ];
    }

    /**
     * Create a relevant entity from the given ID and set of attributes.
     *
     * @param int $id
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Word|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return Word::make(
            $id,
            $attributes[static::WORD]
        );
    }

    /**
     * Find a Word entity by the given word string.
     *
     * @param string $word
     *
     * @return \Blixt\Persistence\Entities\Word|\Blixt\Persistence\Entities\Entity|null
     */
    public function findByWord(string $word): ?Word
    {
        return $this->findBy([
            static::WORD => $word
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
        return $this->getWhere([
            static::WORD => $words->all()
        ]);
    }
}