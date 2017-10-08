<?php

namespace Blixt\Tokenization;

use Illuminate\Support\Collection;

class DefaultTokenizer extends AbstractTokenizer implements TokenizerInterface
{
    /**
     * Tokenize the given string of text into a token collection.
     *
     * @param string $text
     *
     * @return \Illuminate\Support\Collection
     */
    public function tokenize($text)
    {
        $tokens = new Collection();

        $i = 0;
        foreach ($this->split($this->normalize($text)) as $term) {
            $tokens->push(
                new Token($term, $i++)
            );
        }

        return $tokens;
    }

    protected function normalize($text)
    {
        return mb_strtolower(trim($text));
    }

    protected function split($text)
    {
        return preg_split('/[^\\p{L}\\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
}