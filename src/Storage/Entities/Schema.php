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
}