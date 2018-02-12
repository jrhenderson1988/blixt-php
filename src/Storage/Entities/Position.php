<?php

namespace Blixt\Storage\Entities;

class Position extends Entity
{
    /**
     * @var int|null
     */
    protected $occurrenceId;

    /**
     * @var int|null
     */
    protected $position;

    /**
     * @return int|null
     */
    public function getOccurrenceId()
    {
        return $this->occurrenceId;
    }

    /**
     * @param int|null|mixed $occurrenceId
     */
    public function setOccurrenceId($occurrenceId)
    {
        $this->occurrenceId = $occurrenceId !== null ? intval($occurrenceId) : null;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int|null|mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position !== null ? intval($position) : null;
    }
}