<?php

namespace Blixt\Stemming;

interface Stemmer
{
    /**
     * Stem a word.
     *
     * @param string $word
     *
     * @return string
     */
    public function stem(string $word): string;
}
