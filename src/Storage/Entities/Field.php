<?php

namespace Blixt\Storage\Entities;


interface Field
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
    public function getDocumentId();

    /**
     * @param int|mixed $documentId
     */
    public function setDocumentId($documentId);

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int|mixed $attributeId
     */
    public function setAttributeId($attributeId);

    /**
     * @return mixed|null
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);
}