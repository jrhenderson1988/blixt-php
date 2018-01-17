<?php

namespace Blixt\Storage\Entities;

interface Word
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
    public function getWord();

    /**
     * @param string|mixed $word
     */
    public function setWord($word);
}