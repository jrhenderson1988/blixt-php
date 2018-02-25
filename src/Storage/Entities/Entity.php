<?php

namespace Blixt\Storage\Entities;

abstract class Entity
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * Entity constructor.
     *
     * @param int|null $id
     */
    public function __construct($id = null)
    {
        $this->setId($id);
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null|mixed $id
     */
    public function setId($id)
    {
        $this->id = $id !== null ? intval($id) : null;
    }

    /**
     * Fluent getter/setter for id.
     *
     * @param int|null|mixed $id
     *
     * @return $this|int|null
     */
    public function id($id = null)
    {
        if (func_num_args() === 0) {
            return $this->getId();
        }

        $this->setId($id);

        return $this;
    }

    /**
     * Tell if this entity exists, that is, whether its ID property is not null.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->getId() !== null;
    }

    /**
     * Make an instance of the static entity, given a set of attributes.
     *
     * @param array|object $attributes
     *
     * @return static
     */
    public static function make($attributes)
    {
        $word = new static();

        foreach ((array) $attributes as $key => $value) {
            foreach (static::getAttributeMappings() as $method => $mappings) {
                if (is_array($mappings) && in_array($key, $mappings) || $key == $mappings) {
                    $word->$method($value);
                }
            }
        }

        return $word;
    }

    /**
     * The default set of attribute mappings, maps the fields 'id' and 'setId' to the setId() method.
     *
     * @return array
     */
    protected static function getAttributeMappings()
    {
        return [
            'setId' => ['id', 'setId']
        ];
    }
}