<?php

namespace Blixt\Storage\Entities;

interface Document
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
     * @return int|mixed
     */
    public function getSchemaId();

    /**
     * @param int|mixed $schemaId
     */
    public function setSchemaId($schemaId);

    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @param mixed $key
     */
    public function setKey($key);
}