<?php

namespace Blixt\Storage\Entities;

class Position extends Entity
{
    /**
     * @var int
     */
    protected $occurrenceId;

    /**
     * @var int
     */
    protected $position;

    /**
     * Position constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $occurrenceId
     * @param int|mixed $position
     */
    public function __construct($id, $occurrenceId, $position)
    {
        parent::__construct($id);

        $this->setOccurrenceId($occurrenceId);
        $this->setPosition($position);
    }

    /**
     * @return int
     */
    public function getOccurrenceId()
    {
        return $this->occurrenceId;
    }

    /**
     * @param int|mixed $occurrenceId
     */
    public function setOccurrenceId($occurrenceId)
    {
        $this->occurrenceId = intval($occurrenceId);
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int|mixed $position
     */
    public function setPosition($position)
    {
        $this->position = intval($position);
    }
}