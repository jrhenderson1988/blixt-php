<?php

namespace Blixt\Tokenization;

use Illuminate\Support\Collection;

interface Tokenizer
{
    /**
     * Tokenize the given string of text into a token collection.
     *
     * @param string $text
     *
     * @return \Illuminate\Support\Collection
     */
    public function tokenize(string $text): Collection;
}
