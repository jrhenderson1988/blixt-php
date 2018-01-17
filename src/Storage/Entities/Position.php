<?php

namespace Blixt\Storage\Entities;

interface Position
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int|mixed $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getOccurrenceId();

    /**
     * @param int|mixed $occurrenceId
     */
    public function setOccurrenceId($occurrenceId);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int|mixed $position
     */
    public function setPosition($position);
}