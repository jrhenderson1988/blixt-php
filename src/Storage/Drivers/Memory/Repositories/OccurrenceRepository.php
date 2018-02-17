<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Repositories\OccurrenceRepository as OccurrenceRepositoryInterface;

class OccurrenceRepository implements OccurrenceRepositoryInterface
{
    const TABLE = 'occurrences';
    const FIELD_FIELD_ID = 'field_id';
    const FIELD_TERM_ID = 'term_id';
    const FIELD_FREQUENCY = 'frequency';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * OccurrenceRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function save(Occurrence $occurrence)
    {
        return $occurrence->exists() ? $this->update($occurrence) : $this->create($occurrence);
    }

    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    protected function create(Occurrence $occurrence)
    {
        $attributes = $this->getAttributes($occurrence);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    protected function update(Occurrence $occurrence)
    {
        $attributes = $this->getAttributes($occurrence);

        $this->storage->update(static::TABLE, $occurrence->getId(), $attributes);

        return $occurrence;
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
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     *
     * @return array
     */
    protected function getAttributes(Occurrence $occurrence)
    {
        return [
            static::FIELD_FIELD_ID => $occurrence->getFieldId(),
            static::FIELD_TERM_ID => $occurrence->getTermId(),
            static::FIELD_FREQUENCY => $occurrence->getFrequency()
        ];
    }
}