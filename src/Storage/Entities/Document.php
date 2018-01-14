<?php

namespace Blixt\Storage\Entities;

/**
 * @Entity
 * @Table(
 *     name="documents",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="uq_documents_schema_id_key", columns={"schema_id", "key"})
 *     }
 * )
 */
class Document
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", name="schema_id")
     * @var int|mixed
     */
    private $schemaId;

    /**
     * @Column(type="string")
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