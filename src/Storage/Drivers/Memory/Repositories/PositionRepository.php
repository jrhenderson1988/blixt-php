<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Position;
use Blixt\Storage\Repositories\PositionRepository as PositionRepositoryInterface;

class PositionRepository implements PositionRepositoryInterface
{
    const TABLE = 'positions';
    const FIELD_OCCURRENCE_ID = 'occurrence_id';
    const FIELD_POSITION = 'position';

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /**
     * PositionRepository constructor.
     *
     * @param \Blixt\Storage\Drivers\Memory\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public function save(Position $position)
    {
        return $position->exists() ? $this->update($position) : $this->create($position);
    }

    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    protected function create(Position $position)
    {
        $attributes = $this->getAttributes($position);

        $id = $this->storage->insert(static::TABLE, $attributes);

        return $this->map($id, $attributes);
    }

    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    protected function update(Position $position)
    {
        $attributes = $this->getAttributes($position);

        $this->storage->update(static::TABLE, $position->getId(), $attributes);

        return $position;
    }

    /**
     * @param int   $key
     * @param array $row
     *
     * @return \Blixt\Storage\Entities\Position
     */
    protected function map($key, array $row)
    {
        return new Position(
            $key,
            $row[static::FIELD_OCCURRENCE_ID],
            $row[static::FIELD_POSITION]
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Position $position
     *
     * @return array
     */
    protected function getAttributes(Position $position)
    {
        return [
            static::FIELD_OCCURRENCE_ID => $position->getOccurrenceId(),
            static::FIELD_POSITION => $position->getPosition()
        ];
    }
}