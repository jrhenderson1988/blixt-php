<?php

namespace Blixt\Models;

class Column extends Model
{
    /**
     * @var int
     */
    protected $schemaId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $weight;

    /**
     * Column constructor.
     *
     * @param int|mixed    $id
     * @param int|mixed    $schemaId
     * @param string|mixed $name
     * @param float|mixed  $weight
     */
    public function __construct($id, $schemaId, $name, $weight = 1)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setName($name);
        $this->setWeight($weight);
    }

    /**
     * Set the Schema ID.
     *
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = intval($schemaId);
    }

    /**
     * Get the Schema ID.
     *
     * @return int
     */
    public function getSchemaId()
    {
        return $this->schemaId;
    }

    /**
     * Set the name.
     *
     * @param string|mixed $name
     */
    public function setName($name)
    {
        $this->name = strval($name);
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the weight.
     *
     * @param float|mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = floatval($weight);
    }

    /**
     * Get the weight.
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }
}