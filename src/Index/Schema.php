<?php

namespace Blixt\Index;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Schema
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * Schema constructor.
     *
     * @param \Illuminate\Support\Collection|array $columns
     */
    public function __construct($columns = null)
    {
        $this->columns = new Collection();

        if (!is_null($columns)) {
            $this->setColumns($columns);
        }
    }

    /**
     * Set the columns with the given array or collection.
     *
     * @param \Illuminate\Support\Collection|array $columns
     *
     * @throws \InvalidArgumentException
     */
    public function setColumns($columns)
    {
        if (!$columns instanceof Collection && !is_array($columns)) {
            throw new InvalidArgumentException(
                "The columns provided must be an array or a collection."
            );
        }

        $columns = is_array($columns) ? new Collection($columns) : $columns;

        $columns->each(function ($column) {
            $this->addColumn($column);
        });
    }

    /**
     * Add a column to the schema definition.
     *
     * @param \Blixt\Index\Column $column
     *
     * @throws \InvalidArgumentException
     */
    public function addColumn($column)
    {
        if (!$column instanceof Column) {
            $args = func_get_args();

            if (count($args) <= 0) {
                throw new InvalidArgumentException(
                    "At least one argument must be provided."
                );
            } elseif (!is_string($args[0])) {
                throw new InvalidArgumentException(
                    "If a Column is not provided, the first argument must a string representing the name of the column."
                );
            }

            $column = new Column(
                strval($args[0]),
                isset($args[1]) ? !!$args[1] : true,
                isset($args[2]) ? !!$args[2] : false,
                isset($args[3]) ? floatval($args[3]) : 1.0
            );
        }

        $this->columns->put(
            $column->getName(), $column
        );
    }

    /**
     * Get the columns.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getColumns()
    {
        return $this->columns;
    }
}