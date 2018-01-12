<?php

namespace Blixt\Tokenization;

interface Tokenizer
{
    /**
     * Tokenize the given string of text into a token collection.
     *
     * @param string $text
     *
     * @return \Illuminate\Support\Collection
     */
    public function tokenize($text);
}