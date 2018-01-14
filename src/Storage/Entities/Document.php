<?php

namespace Blixt\Storage\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="documents",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uq_schema_id_key", columns={"schema_id", "key"})
 *     }
 * )
 */
class Document
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
     * @var int|mixed
     */
    private $schemaId;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var mixed
     */
    private $key;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|mixed
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
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}