<?php

namespace Blixt\Storage\Entities;

use Illuminate\Support\Collection;

class Schema extends Entity
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var \Illuminate\Support\Collection|null
     */
    protected $columns;

    /**
     * Schema constructor.
     *
     * @param int|null|mixed    $id
     * @param string|null|mixed $name
     */
    public function __construct($id = null, $name = null)
    {
        parent::__construct($id);

        $this->setName($name);
        $this->setColumns(new Collection());
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null|mixed $name
     */
    public function setName($name)
    {
        $this->name = $name !== null ? strval($name) : null;
    }

    /**
     * Fluent getter/setter for name.
     *
     * @param string|null|mixed $name
     *
     * @return $this|null|string
     */
    public function name($name = null)
    {
        if (func_num_args() === 0) {
            return $this->getName();
        }

        $this->setName($name);

        return $this;
    }

    /**
     * @param \Illuminate\Support\Collection $columns
     */
    public function setColumns(Collection $columns)
    {
        $this->columns = $columns->filter(function (Column $column) {
            return $column->getSchemaId() === $this->getId();
        });
    }

    /**
     * @return \Illuminate\Support\Collection|null
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Fluent getter/setter for columns.
     *
     * @param \Illuminate\Support\Collection|null $columns
     *
     * @return $this|\Illuminate\Support\Collection|null
     */
    public function column(Collection $columns = null)
    {
        if (func_num_args() === 0) {
            return $this->getColumns();
        }

        $this->setColumns($columns);

        return $this;
    }

    /**
     * Create a new schema from the set of attributes given.
     *
     * @param array|object $attributes
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    public static function make($attributes)
    {
        $schema = new static();

        foreach ((array) $attributes as $key => $value) {
            if (in_array($key, ['id', 'setId'])) {
                $schema->setId($value);
            } elseif (in_array($key, ['name', 'setName'])) {
                $schema->setName($value);
            }
        }

        return $schema;
    }
}