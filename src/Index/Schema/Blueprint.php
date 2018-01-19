<?php

namespace Blixt\Index\Schema;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Blueprint
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
        $this->setColumns(! is_null($columns) ? $columns : new Collection());
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
     * @param \Blixt\Index\Schema\Column|string $column
     * @param bool                              $indexed
     * @param bool                              $stored
     */
    public function addColumn($column, $indexed = true, $stored = false)
    {
        if (!$column instanceof Column) {
            if (!is_string($column)) {
                throw new InvalidArgumentException(
                    "The column provided must be a string or an instance of " . Column::class . "."
                );
            }

            $column = new Column($column, $indexed, $stored);
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