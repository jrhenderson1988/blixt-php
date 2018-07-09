<?php

namespace Blixt\Tokenization;

use Illuminate\Support\Collection;

interface Tokenizer
{
    /**
     * Tokenize the given string of text into a collection of tokens. If a set of prefixes are provided, they are to be
     * considered valid prefixes to each word in the text and should not be removed.
     *
     * @param string $text
     * @param array $prefixes
     *
     * @return \Illuminate\Support\Collection
     */
    public function tokenize(string $text, array $prefixes = []): Collection;
}
