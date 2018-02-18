<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Entities\Entity;
use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Repositories\OccurrenceRepository as OccurrenceRepositoryInterface;

class OccurrenceRepository extends AbstractRepository implements OccurrenceRepositoryInterface
{
    const ENTITY = Occurrence::class;
    const TABLE = 'occurrences';
    const FIELD_FIELD_ID = 'field_id';
    const FIELD_TERM_ID = 'term_id';
    const FIELD_FREQUENCY = 'frequency';

    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     *
     * @return \Blixt\Storage\Entities\Occurrence
     * @throws \Blixt\Exceptions\StorageException
     */
    public function save(Occurrence $occurrence)
    {
        return $this->saveEntity($occurrence);
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    protected function map($key, array $row)
    {
        return new Occurrence(
            $key,
            $row[static::FIELD_FIELD_ID],
            $row[static::FIELD_TERM_ID],
            $row[static::FIELD_FREQUENCY]
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
            static::FIELD_FIELD_ID => $entity->getFieldId(),
            static::FIELD_TERM_ID => $entity->getTermId(),
            static::FIELD_FREQUENCY => $entity->getFrequency()
        ];
    }
}