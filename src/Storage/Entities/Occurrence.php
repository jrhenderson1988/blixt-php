<?php

namespace Blixt\Storage\Entities;

/**
 * @Entity
 * @Table(
 *     name="occurrences",
 *     uniqueConstraints={
 *         @UniqueConstraint(name="uq_occurrences_field_id_term_id", columns={"field_id", "term_id"})
 *     }
 * )
 */
class Occurrence
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer", name="field_id")
     * @var int
     */
    private $fieldId;

    /**
     * @Column(type="integer", name="term_id")
     * @var int
     */
    private $termId;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $frequency;

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
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * @param int|mixed $fieldId
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = intval($fieldId);
    }

    /**
     * @return int
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * @param int|mixed $termId
     */
    public function setTermId($termId)
    {
        $this->termId = intval($termId);
    }

    /**
     * @return int
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param int|mixed $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = intval($frequency);
    }
}