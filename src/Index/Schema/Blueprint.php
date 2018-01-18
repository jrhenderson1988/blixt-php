<?php

namespace Blixt\Index\Schema;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Blueprint
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columnDefinitions;

    /**
     * Schema constructor.
     *
     * @param \Illuminate\Support\Collection|array $columns
     */
    public function __construct($columns = null)
    {
        $this->setColumnDefinitions(!is_null($columns) ? $columns : new Collection());
    }

    /**
     * Set the columns with the given array or collection.
     *
     * @param \Illuminate\Support\Collection|array $columnDefinitions
     *
     * @throws \InvalidArgumentException
     */
    public function setColumnDefinitions($columnDefinitions)
    {
        if (!$columnDefinitions instanceof Collection && !is_array($columnDefinitions)) {
            throw new InvalidArgumentException(
                "The columns provided must be an array or a collection."
            );
        }

        $columnDefinitions = is_array($columnDefinitions) ? new Collection($columnDefinitions) : $columnDefinitions;

        $columnDefinitions->each(function ($column) {
            $this->addColumnDefinition($column);
        });
    }

    /**
     * Add a column to the schema definition.
     *
     * @param \Blixt\Index\Schema\ColumnDefinition|string $columnDefinition
     * @param bool                                        $indexed
     * @param bool                                        $stored
     */
    public function addColumnDefinition($columnDefinition, $indexed = true, $stored = false)
    {
        if (!$columnDefinition instanceof ColumnDefinition) {
            if (!is_string($columnDefinition)) {
                throw new InvalidArgumentException(
                    "The column definition provided must be a string or an instance of " . ColumnDefinition::class . "."
                );
            }

            $columnDefinition = new ColumnDefinition($columnDefinition, $indexed, $stored);
        }

        $this->columnDefinitions->put($columnDefinition->getName(), $columnDefinition);
    }

    /**
     * Get the columns.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getColumnDefinitions()
    {
        return $this->columnDefinitions;
    }
}