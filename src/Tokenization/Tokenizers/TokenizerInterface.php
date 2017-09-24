<?php

namespace Blixt\Tokenization\Tokenizers;

interface TokenizerInterface
{
    /**
     * Tokenize the given string of text into a token collection.
     *
     * @param string $text
     *
     * @return \Blixt\Tokenization\TokenCollection
     */
    public function tokenize($text);
}