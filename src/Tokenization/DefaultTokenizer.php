<?php

namespace Blixt\Tokenization;

use Blixt\Stemming\Stemmer;
use Illuminate\Support\Collection;

class DefaultTokenizer implements Tokenizer
{
    /**
     * @var \Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    /**
     * DefaultTokenizer constructor.
     *
     * @param \Blixt\Stemming\Stemmer $stemmer
     */
    public function __construct(Stemmer $stemmer)
    {
        $this->stemmer = $stemmer;
    }

    /**
     * Tokenize the given string of text into a token collection.
     *
     * @param string $text
     *
     * @return \Illuminate\Support\Collection
     */
    public function tokenize(string $text): Collection
    {
        $tokens = new Collection();

        $i = 0;
        $this->split($text)->each(function ($word) use (&$tokens, &$i) {
            $tokens->push(
                new Token($this->stemmer->stem($word), $i++)
            );
        });

        return $tokens;
    }

    /**
     * Normalize the given text by converting it to lowercase, trimming whitespace around it and removing all characters
     * that are not alphanumeric or whitespace.
     *
     * @param string $text
     *
     * @return string
     */
    protected function normalize(string $text): string
    {
        return preg_replace('/[^\\p{L}\\p{N}\\s]/', '', mb_strtolower(trim($text)));
    }

    /**
     * Split the given text into a collection of words.
     *
     * @param string $text
     *
     * @return \Illuminate\Support\Collection
     */
    protected function split(string $text): Collection
    {
        $words = new Collection(explode(' ', $this->normalize($text)));

        return $words->filter(function ($word) {
            return ! empty(trim($word));
        });
    }
}
