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
     * Mappings of the methods to sets of keys. That method will be used to set a property identified by one of the keys
     * when using the make method to create an instance of the entity.
     *
     * @return array
     */
    public static function getAttributeMappings()
    {
        return array_merge(parent::getAttributeMappings(), [
            'setSchemaId' => ['schema_id', 'schemaId', 'setSchemaId'],
            'setName' => ['name', 'setName']
        ]);
    }
}