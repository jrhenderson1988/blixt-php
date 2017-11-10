<?php

namespace Blixt\Stemming;

interface StemmerContract
{
    /**
     * Stem a word.
     *
     * @param string $word
     *
     * @return string
     */
    public function stem($word);
}