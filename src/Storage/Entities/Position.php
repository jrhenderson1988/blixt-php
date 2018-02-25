<?php

namespace Blixt\Storage\Entities;

use Blixt\Storage\Entities\Concerns\BelongsToOccurrence;

class Position extends Entity
{
    use BelongsToOccurrence;

    /**
     * @var int|null
     */
    protected $position;

    /**
     * Position constructor.
     *
     * @param int|null|mixed $id
     * @param int|null|mixed $occurrenceId
     * @param int|null|mixed $position
     */
    public function __construct($id = null, $occurrenceId = null, $position = null)
    {
        parent::__construct($id);

        $this->setOccurrenceId($occurrenceId);
        $this->setPosition($position);
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

    /**
     * Fluent getter/setter for positon.
     *
     * @param int|null|mixed $position
     *
     * @return $this|int|null
     */
    public function position($position = null)
    {
        if (func_num_args() === 0) {
            return $this->getPosition();
        }

        $this->setPosition($position);

        return $this;
    }

    /**
     * Create a new position from the set of attributes given.
     *
     * @param array|object $attributes
     *
     * @return \Blixt\Storage\Entities\Position
     */
    public static function make($attributes)
    {
        $position = new static();

        foreach ((array) $attributes as $key => $value) {
            if (in_array($key, ['id', 'setId'])) {
                $position->setId($value);
            } elseif (in_array($key, ['occurrence_id', 'occurrenceId', 'setOccurrenceId'])) {
                $position->setOccurrenceId($value);
            } elseif (in_array($key, ['position', 'setPosition'])) {
                $position->setPosition($value);
            }
        }

        return $position;
    }
}