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
     * @param \Blixt\Index\Column|string $column
     * @param bool                       $indexed
     * @param bool                       $stored
     * @param float                      $weight
     */
    public function addColumn($column, $indexed = true, $stored = false, $weight = 1.0)
    {
        if (!$column instanceof Column) {
            if (!is_string($column)) {
                throw new InvalidArgumentException(
                    "The column provided must be an instance of '" . Column::class . "' or a string."
                );
            }

            $column = new Column($column, $indexed, $stored, $weight);
        }

        $this->columns->put($column->getName(), $column);
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