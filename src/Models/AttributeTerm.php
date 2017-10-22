<?php

namespace Blixt\Models;

class AttributeTerm extends Model
{
    /**
     * @var int
     */
    protected $attributeId;

    /**
     * @var int
     */
    protected $termId;

    /**
     * AttributeTerm constructor.
     *
     * @param int|mixed $id
     * @param int|mixed $attributeId
     * @param int|mixed $termId
     */
    public function __construct($id, $attributeId, $termId)
    {
        parent::__construct($id);

        $this->setAttributeId($attributeId);
        $this->setTermId($termId);
    }

    /**
     * Set the Attribute ID.
     *
     * @param int|mixed $attributeId
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = intval($attributeId);
    }

    /**
     * Get the Attribute ID.
     *
     * @return int
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Set the Term ID.
     *
     * @param int|mixed $termId
     */
    public function setTermId($termId)
    {
        $this->termId = intval($termId);
    }

    /**
     * Get the Term ID.
     *
     * @return int
     */
    public function getTermId()
    {
        return $this->termId;
    }
}