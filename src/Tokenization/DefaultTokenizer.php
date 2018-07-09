<?php

namespace Blixt\Tokenization;

use Illuminate\Support\Collection;

class DefaultTokenizer implements Tokenizer
{
    /**
     * Tokenize the given string of text into a token collection.
     *
     * @param string $text
     * @param array $prefixes
     *
     * @return \Illuminate\Support\Collection
     */
    public function tokenize(string $text, array $prefixes = []): Collection
    {
        $prefixes = $this->buildPrefixes($prefixes);

        $i = 0;
        return $this->split($text, $prefixes)
            ->map(function ($word) use (&$i) {
                [$prefix, $text] = $word;

                return new Token($text, $i++, $prefix);
            });
    }

    /**
     * Create a collection of prefixes from the given array. Each prefix is normalized by trimming it and converting it
     * to lowercase. All invalid filters are conveniently removed.
     *
     * @param array $prefixes
     *
     * @return \Illuminate\Support\Collection
     */
    protected function buildPrefixes(array $prefixes): Collection
    {
        return Collection::make($prefixes)
            ->map(function ($prefix) {
                return trim(mb_strtolower($prefix));
            })
            ->filter();
    }

    /**
     * Detect a prefix on and normalize the given word. The given array of prefixes is
     *
     * Normalize the given word by converting it to lowercase, trimming whitespace around it and removing all characters
     * that are not alphanumeric or whitespace.
     *
     * @param string $word
     * @param \Illuminate\Support\Collection $prefixes
     *
     * @return array
     */
    protected function normalize(string $word, Collection $prefixes): array
    {
        $word = trim(mb_strtolower($word));

        $prefix = $prefixes->first(function ($prefix) use ($word) {
            return empty($prefix) ? false : mb_strpos($word, $prefix) === 0;
        }) ?? '';

        $word = empty($prefix) ? $word : mb_substr($word, mb_strlen($prefix));

        return [$prefix, preg_replace('/[^\\p{L}\\p{N}\\s]/', '', $word)];
    }

    /**
     * Split the given text into a collection of words.
     *
     * @param string $text
     * @param \Illuminate\Support\Collection $prefixes
     *
     * @return \Illuminate\Support\Collection
     */
    protected function split(string $text, Collection $prefixes): Collection
    {
        return Collection::make(preg_split('/\s+/', $text))
            ->map(function ($word) use ($prefixes) {
                return $this->normalize($word, $prefixes);
            })
            ->filter();
    }
}
