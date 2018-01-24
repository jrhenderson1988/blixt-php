<?php

namespace Blixt\Storage\Repositories;

interface WordRepository
{
    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function findByWord($word);

    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function create($word);
}