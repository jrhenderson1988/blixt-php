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
        foreach (preg_split('/[^\\p{L}\\p{N}]+/u', mb_strtolower(trim($text)), -1, PREG_SPLIT_NO_EMPTY) as $term) {
            $tokens->push(
                new Token($term, $i++)
            );
        }

        return $tokens;
    }
}