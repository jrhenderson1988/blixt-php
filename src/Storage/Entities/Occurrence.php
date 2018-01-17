<?php

namespace Blixt\Storage\Entities;

interface Occurrence
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int|mixed $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getFieldId();

    /**
     * @param int|mixed $fieldId
     */
    public function setFieldId($fieldId);

    /**
     * @return int
     */
    public function getTermId();

    /**
     * @param int|mixed $termId
     */
    public function setTermId($termId);

    /**
     * @return int
     */
    public function getFrequency();

    /**
     * @param int|mixed $frequency
     */
    public function setFrequency($frequency);
}