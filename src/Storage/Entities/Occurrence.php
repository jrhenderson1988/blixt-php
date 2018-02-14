<?php

namespace Blixt\Storage\Entities;

class Occurrence extends Entity
{
    /**
     * @var int|null
     */
    protected $fieldId;

    /**
     * @var int|null
     */
    protected $termId;

    /**
     * @var int|null
     */
    protected $frequency;

    /**
     * Occurrence constructor.
     *
     * @param int|null|mixed $id
     * @param int|null|mixed $fieldId
     * @param int|null|mixed $termId
     * @param int|null|mixed $frequency
     */
    public function __construct($id = null, $fieldId = null, $termId = null, $frequency = null)
    {
        parent::__construct($id);

        $this->setFieldId($fieldId);
        $this->setTermId($termId);
        $this->setFrequency($frequency);
    }

    /**
     * @return int|null
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * @param int|null|mixed $fieldId
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId !== null ? intval($fieldId) : null;
    }

    /**
     * @return int|null
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * @param int|null|mixed $termId
     */
    public function setTermId($termId)
    {
        $this->termId = $termId !== null ? intval($termId) : null;
    }

    /**
     * @return int|null
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param int|null|mixed $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency !== null ? intval($frequency) : null;
    }
}