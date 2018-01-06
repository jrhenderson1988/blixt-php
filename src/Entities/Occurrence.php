<?php

namespace Blixt\Entities;

class Occurrence extends Entity
{
    /**
     * @var int
     */
    protected $presenceId;

    /**
     * @var int
     */
    protected $position;

    /**
     * Occurrence constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $presenceId
     * @param int|mixed $position
     */
    public function __construct($id, $presenceId, $position)
    {
        parent::__construct($id);

        $this->setPresenceId($presenceId);
        $this->setPosition($position);
    }

    /**
     * Get the Presence ID.
     *
     * @return int
     */
    public function getPresenceId()
    {
        return $this->presenceId;
    }

    /**
     * Set the Presence ID.
     *
     * @param int|mixed $presenceId
     */
    public function setPresenceId($presenceId)
    {
        $this->presenceId = intval($presenceId);
    }

    /**
     * Get the position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the position.
     *
     * @param int|mixed $position
     */
    public function setPosition($position)
    {
        $this->position = intval($position);
    }
}