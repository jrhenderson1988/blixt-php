<?php

namespace Blixt\Storage\Entities;

interface Schema
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
     * @return string
     */
    public function getName();

    /**
     * @param string|mixed $name
     */
    public function setName($name);
}