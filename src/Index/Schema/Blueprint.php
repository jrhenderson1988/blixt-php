<?php

namespace Blixt\Index\Schema;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Blueprint
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columnDefinitions;

    /**
     * Schema constructor.
     *
     * @param string|mixed                         $name
     * @param \Illuminate\Support\Collection|array $columnDefinitions
     */
    public function __construct($name, $columnDefinitions = null)
    {
        $this->setName($name);
        $this->setColumnDefinitions(! is_null($columnDefinitions) ? $columnDefinitions : new Collection());
    }

    /**
     * Set the name of the blueprint.
     *
     * @param string|mixed $name
     */
    public function setName($name)
    {
        $this->name = strval($name);
    }

    /**
     * Get the name of the blueprint.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the column definitions to the given array or collection.
     *
     * @param \Illuminate\Support\Collection|array $columnDefinitions
     *
     * @throws \InvalidArgumentException
     */
    public function setColumnDefinitions($columnDefinitions)
    {
        if (! $columnDefinitions instanceof Collection && ! is_array($columnDefinitions)) {
            throw new InvalidArgumentException(
                "The column definitions provided must be an array or a collection."
            );
        }

        $this->columnDefinitions = new Collection();

        (new Collection($columnDefinitions))->each(function ($columnDefinition) {
            $this->addColumnDefinition($columnDefinition);
        });
    }

    /**
     * Add a column definition to the schema blueprint.
     *
     * @param \Blixt\Index\Schema\ColumnDefinition|string $name
     * @param bool                                        $isIndexed
     * @param bool                                        $isStored
     */
    public function addColumnDefinition($name, $isIndexed = true, $isStored = false)
    {
        $columnDefinition = $this->createColumnDefinition($name, $isIndexed, $isStored);

        $this->columnDefinitions->put(
            $columnDefinition->getName(), $columnDefinition
        );
    }

    /**
     * Get the column definitions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getColumnDefinitions()
    {
        return $this->columnDefinitions;
    }

    /**
     * Create a column definition from the given data. If the first parameter is already a column definiton, it is
     * returned as is.
     *
     * @param \Blixt\Index\Schema\ColumnDefinition|string $name
     * @param bool                                        $isIndexed
     * @param bool                                        $isStored
     *
     * @return \Blixt\Index\Schema\ColumnDefinition
     */
    protected function createColumnDefinition($name, $isIndexed = true, $isStored = false)
    {
        if ($name instanceof ColumnDefinition) {
            return $name;
        }

        if (! is_string($name) || empty($name)) {
            throw new InvalidArgumentException(
                'Invalid column definition name, expected non-empty string.'
            );
        }

        return new ColumnDefinition($name, $isIndexed, $isStored);
    }
}