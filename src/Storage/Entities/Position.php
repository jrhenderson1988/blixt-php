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
     * Mappings of the methods to sets of keys. That method will be used to set a property identified by one of the keys
     * when using the make method to create an instance of the entity.
     *
     * @return array
     */
    public static function getAttributeMappings()
    {
        return array_merge(parent::getAttributeMappings(), [
            'setOccurrenceId' => ['occurrence_id', 'occurrenceId', 'setOccurrenceId'],
            'setPosition' => ['position', 'setPosition']
        ]);
    }
}