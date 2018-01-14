<?php

namespace Blixt\Storage\Entities;

/**
 * @Entity
 * @Table(
 *     name="positions",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="uq_positions_occurrence_id_position", columns={"occurrence_id", "position"})
 *     }
 * )
 */
class Position
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", name="occurrence_id")
     * @var int
     */
    private $occurrenceId;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $position;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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