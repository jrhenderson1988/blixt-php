<?php

namespace Blixt\Storage\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="columns",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uq_columns_schema_id_name", columns={"schema_id", "name"})
 *     }
 * )
 */
class Column
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="schema_id")
     * @var int
     */
    private $schemaId;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean", name="is_indexed")
     * @var bool
     */
    protected $indexed;

    /**
     * @ORM\Column(type="boolean", name="is_stored")
     * @var bool
     */
    protected $stored;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSchemaId()
    {
        return $this->schemaId;
    }

    /**
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = intval($schemaId);
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
     * @return bool
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * @param bool|mixed $indexed
     */
    public function setIndexed($indexed)
    {
        $this->indexed = !! $indexed;
    }

    /**
     * @return bool
     */
    public function isStored()
    {
        return $this->stored;
    }

    /**
     * @param bool|mixed $stored
     */
    public function setStored($stored)
    {
        $this->stored = !! $stored;
    }
}