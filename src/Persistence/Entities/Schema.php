<?php

namespace Blixt\Persistence\Entities;

use Illuminate\Support\Collection;

class Schema extends Entity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * Schema constructor.
     *
     * @param int|null $id
     * @param string   $name
     */
    public function __construct(?int $id, string $name)
    {
        parent::__construct($id);

        $this->setName($name);
        $this->setColumns(new Collection());
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set the columns to the given collection. Filters out columns that don't have a schema ID matching this schema.
     *
     * @param \Illuminate\Support\Collection $columns
     */
    public function setColumns(Collection $columns): void
    {
        $this->columns = $columns->filter(function (Column $column) {
            return $column->getSchemaId() === $this->getId();
        });
    }

    /**
     * Get this schema's columns.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * Tell if this schema actually has some columns.
     *
     * @return bool
     */
    public function hasColumns()
    {
        return $this->columns->isNotEmpty();
    }

    /**
     * Factory method to create a new Schema.
     *
     * @param string $name
     *
     * @return \Blixt\Persistence\Entities\Schema
     */
    public static function create(string $name): Schema
    {
        return new static(null, $name);
    }

    /**
     * Factory method to make a Schema from an existing record.
     *
     * @param int    $id
     * @param string $name
     *
     * @return \Blixt\Persistence\Entities\Schema
     */
    public static function make(int $id, string $name): Schema
    {
        return new static($id, $name);
    }
}
