<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToOccurrence;

class Position extends Entity
{
    use BelongsToOccurrence;

    /**
     * @var int
     */
    protected $position;

    /**
     * Position constructor.
     *
     * @param int|null $id
     * @param int      $occurrenceId
     * @param int      $position
     */
    public function __construct(?int $id, int $occurrenceId, int $position)
    {
        parent::__construct($id);

        $this->setOccurrenceId($occurrenceId);
        $this->setPosition($position);
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * Factory method to create a new Position.
     *
     * @param int $occurrenceId
     * @param int $position
     *
     * @return \Blixt\Persistence\Entities\Position
     */
    public static function create(int $occurrenceId, int $position): Position
    {
        return new static(null, $occurrenceId, $position);
    }

    /**
     * Factory method to make a Position from an existing record.
     *
     * @param int $id
     * @param int $occurrenceId
     * @param int $position
     *
     * @return \Blixt\Persistence\Entities\Position
     */
    public static function make(int $id, int $occurrenceId, int $position): Position
    {
        return new static($id, $occurrenceId, $position);
    }
}