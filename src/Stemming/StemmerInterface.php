<?php

namespace Blixt\Stemming;

interface StemmerInterface
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