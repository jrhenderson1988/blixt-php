<?php

namespace Blixt\Storage\Entities;

class Occurrence extends Entity
{
    /**
     * @var int
     */
    protected $fieldId;

    /**
     * @var int
     */
    protected $termId;

    /**
     * @var int
     */
    protected $frequency;

    /**
     * Presence constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $fieldId
     * @param int|mixed $termId
     * @param int|mixed $frequency
     */
    public function __construct($id, $fieldId, $termId, $frequency)
    {
        parent::__construct($id);

        $this->setFieldId($fieldId);
        $this->setTermId($termId);
        $this->setFrequency($frequency);
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