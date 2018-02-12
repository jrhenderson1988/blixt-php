<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Word;
use Illuminate\Support\Collection;

interface WordRepository
{
    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function findByWord($word);

    /**
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByWords(Collection $words);

    /**
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function create($word);

    /**
     * @param \Blixt\Storage\Entities\Word $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    public function save(Word $word);
}