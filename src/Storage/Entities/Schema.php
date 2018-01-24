<?php

namespace Blixt\Storage\Entities;

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
     * @param int|mixed    $id
     * @param string|mixed $name
     */
    public function __construct($id, $name)
    {
        parent::__construct($id);

        $this->setName($name);
        $this->setColumns(new Collection());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|mixed $name
     */
    public function setName($name)
    {
        $this->name = strval($name);
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
     * @return \Illuminate\Support\Collection
     */
    public function getColumns()
    {
        return $this->columns;
    }
}