<?php

namespace Blixt\Storage\Entities;

interface Column
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
    public function getSchemaId();

    /**
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string|mixed $name
     */
    public function setName($name);

    /**
     * @return bool
     */
    public function isIndexed();

    /**
     * @param bool|mixed $indexed
     */
    public function setIndexed($indexed);

    /**
     * @return bool
     */
    public function isStored();

    /**
     * @param bool|mixed $stored
     */
    public function setStored($stored);
}