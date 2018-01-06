<?php

namespace Blixt\Entities;

class Presence extends Entity
{
    /**
     * @var int
     */
    protected $fieldId;

    /**
     * @var int
     */
    protected $wordId;

    /**
     * @var int
     */
    protected $frequency;

    /**
     * Presence constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $fieldId
     * @param int|mixed $wordId
     * @param int|mixed $frequency
     */
    public function __construct($id, $fieldId, $wordId, $frequency)
    {
        parent::__construct($id);

        $this->setFieldId($fieldId);
        $this->setWordId($wordId);
        $this->setFrequency($frequency);
    }

    /**
     * Get the Field ID.
     *
     * @return int
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * Set the Field ID.
     *
     * @param int|mixed $fieldId
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = intval($fieldId);
    }

    /**
     * Get the Word ID.
     *
     * @return int
     */
    public function getWordId()
    {
        return $this->wordId;
    }

    /**
     * Set the Word ID.
     *
     * @param int|mixed $wordId
     */
    public function setWordId($wordId)
    {
        $this->wordId = intval($wordId);
    }

    /**
     * Get the frequency.
     *
     * @return int
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set the frequency.
     *
     * @param int|mixed $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = intval($frequency);
    }
}