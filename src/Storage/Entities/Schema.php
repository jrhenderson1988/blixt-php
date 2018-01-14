<?php

namespace Blixt\Storage\Entities;

/**
 * @Entity
 * @Table(
 *     name="schemas",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="uq_schemas_name", columns={"name"})
 *     }
 * )
 */
class Schema
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
}