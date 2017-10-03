<?php

namespace Blixt\Stemming;

/**
 * This class is a modified version of the one written by Richard Heyes, found here:
 *
 * https://tartarus.org/martin/PorterStemmer/
 *
 * It's primary difference is that this class has been modified to fit the StemmerInterface and to function using
 * traditional, non-static methods. Other than that, the class name/namespace has been changed and the formatting/code
 * style has been updated to reflect PSR-2 standards and to remove the use of keywords like AND and OR in place of &&
 * and || etc. Oh, changes to support multi-byte functions too.
 *
 * @package RainSearch\Stemmers
 */
class EnglishStemmer implements StemmerInterface
{
    /**
     * Regex for matching a consonant.
     *
     * @var string
     */
    const CONSONANT = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';

    /**
     * Regex for matching a vowel.
     *
     * @var string
     */
    const VOWEL = '(?:[aeiou]|(?<![aeiou])y)';

    /**
     * Stems a word. Simple huh?
     *
     * @param  string $word
     *
     * @return string
     */
    public function stem($word)
    {
        if (mb_strlen($word) <= 2) {
            return $word;
        }

        $word = $this->step1a($word);
        $word = $this->step1b($word);
        $word = $this->step1c($word);
        $word = $this->step2($word);
        $word = $this->step3($word);
        $word = $this->step4($word);
        $word = $this->step5a($word);
        $word = $this->step5b($word);

        return $word;
    }

    /**
     * Step 1a - Deal with plurals.
     *
     * @param string $word
     *
     * @return string
     */
    protected function step1a($word)
    {
        if (mb_substr($word, -1) == 's') {
            foreach (['sses' => 'ss', 'ies' => 'i', 'ss' => 'ss', 's' => ''] as $search => $replace) {
                if ($this->replace($word, $search, $replace)) {
                    break;
                }
            }
        }

        return $word;
    }


    /**
     * Step 1b - Deal with past participles.
     *
     * @param string $word
     *
     * @return bool|string
     */
    protected function step1b($word)
    {
        if (mb_substr($word, -2, 1) != 'e' || !$this->replace($word, 'eed', 'ee', 0)) {
            if ((preg_match("#" . self::VOWEL . "+#", mb_substr($word, 0, -3)) && $this->replace($word, 'ing', ''))
                || (preg_match("#" . self::VOWEL . "+#", mb_substr($word, 0, -2)) && $this->replace($word, 'ed', ''))) {

                if (!$this->replace($word, 'at', 'ate')
                    && !$this->replace($word, 'bl', 'ble')
                    && !$this->replace($word, 'iz', 'ize')) {

                    if ($this->doubleConsonant($word)
                        && mb_substr($word, -2) != 'll'
                        && mb_substr($word, -2) != 'ss'
                        && mb_substr($word, -2) != 'zz') {

                        $word = mb_substr($word, 0, -1);

                    } else if ($this->m($word) == 1 && $this->cvc($word)) {
                        $word .= 'e';
                    }
                }
            }
        }

        return $word;
    }


    /**
     * Step 1c - Deal with words that end in 'y' but have vowels. (happy -> happi, sky -> sky).
     *
     * @param string $word
     *
     * @return string
     */
    protected function step1c($word)
    {
        if (mb_substr($word, -1) == 'y' && preg_match("#" . self::VOWEL . "+#", mb_substr($word, 0, -1))) {
            $this->replace($word, 'y', 'i');
        }

        return $word;
    }

    /**
     * Step 2 - Specific endings.
     *
     * @param string $word
     *
     * @return string
     */
    protected function step2($word)
    {
        switch (mb_substr($word, -2, 1)) {
            case 'a':
                $this->replace($word, 'ational', 'ate', 0)
                || $this->replace($word, 'tional', 'tion', 0);
                break;

            case 'c':
                $this->replace($word, 'enci', 'ence', 0)
                || $this->replace($word, 'anci', 'ance', 0);
                break;

            case 'e':
                $this->replace($word, 'izer', 'ize', 0);
                break;

            case 'g':
                $this->replace($word, 'logi', 'log', 0);
                break;

            case 'l':
                $this->replace($word, 'entli', 'ent', 0)
                || $this->replace($word, 'ousli', 'ous', 0)
                || $this->replace($word, 'alli', 'al', 0)
                || $this->replace($word, 'bli', 'ble', 0)
                || $this->replace($word, 'eli', 'e', 0);
                break;

            case 'o':
                $this->replace($word, 'ization', 'ize', 0)
                || $this->replace($word, 'ation', 'ate', 0)
                || $this->replace($word, 'ator', 'ate', 0);
                break;

            case 's':
                $this->replace($word, 'iveness', 'ive', 0)
                || $this->replace($word, 'fulness', 'ful', 0)
                || $this->replace($word, 'ousness', 'ous', 0)
                || $this->replace($word, 'alism', 'al', 0);
                break;

            case 't':
                $this->replace($word, 'biliti', 'ble', 0)
                || $this->replace($word, 'aliti', 'al', 0)
                || $this->replace($word, 'iviti', 'ive', 0);
                break;
        }

        return $word;
    }


    /**
     * Step 3
     *
     * @param string $word
     *
     * @return string
     */
    protected function step3($word)
    {
        switch (mb_substr($word, -2, 1)) {
            case 'a':
                $this->replace($word, 'ical', 'ic', 0);
                break;

            case 's':
                $this->replace($word, 'ness', '', 0);
                break;

            case 't':
                $this->replace($word, 'icate', 'ic', 0)
                || $this->replace($word, 'iciti', 'ic', 0);
                break;

            case 'u':
                $this->replace($word, 'ful', '', 0);
                break;

            case 'v':
                $this->replace($word, 'ative', '', 0);
                break;

            case 'z':
                $this->replace($word, 'alize', 'al', 0);
                break;
        }

        return $word;
    }


    /**
     * Step 4
     *
     * @param string
     *
     * @return string
     */
    protected function step4($word)
    {
        switch (mb_substr($word, -2, 1)) {
            case 'a':
                $this->replace($word, 'al', '', 1);
                break;

            case 'c':
                $this->replace($word, 'ance', '', 1)
                OR $this->replace($word, 'ence', '', 1);
                break;

            case 'e':
                $this->replace($word, 'er', '', 1);
                break;

            case 'i':
                $this->replace($word, 'ic', '', 1);
                break;

            case 'l':
                $this->replace($word, 'able', '', 1)
                || $this->replace($word, 'ible', '', 1);
                break;

            case 'n':
                $this->replace($word, 'ant', '', 1)
                || $this->replace($word, 'ement', '', 1)
                || $this->replace($word, 'ment', '', 1)
                || $this->replace($word, 'ent', '', 1);
                break;

            case 'o':
                if (mb_substr($word, -4) == 'tion' || mb_substr($word, -4) == 'sion') {
                    $this->replace($word, 'ion', '', 1);
                } else {
                    $this->replace($word, 'ou', '', 1);
                }
                break;

            case 's':
                $this->replace($word, 'ism', '', 1);
                break;

            case 't':
                $this->replace($word, 'ate', '', 1)
                || $this->replace($word, 'iti', '', 1);
                break;

            case 'u':
                $this->replace($word, 'ous', '', 1);
                break;

            case 'v':
                $this->replace($word, 'ive', '', 1);
                break;

            case 'z':
                $this->replace($word, 'ize', '', 1);
                break;
        }

        return $word;
    }


    /**
     * Step 5a
     *
     * @param string $word
     *
     * @return string
     */
    protected function step5a($word)
    {
        if (mb_substr($word, -1) == 'e') {
            if ($this->m(mb_substr($word, 0, -1)) > 1) {
                $this->replace($word, 'e', '');
            } else if ($this->m(mb_substr($word, 0, -1)) == 1) {

                if (!$this->cvc(mb_substr($word, 0, -1))) {
                    $this->replace($word, 'e', '');
                }
            }
        }

        return $word;
    }

    /**
     * Step 5b
     *
     * @param string $word
     *
     * @return string
     */
    protected function step5b($word)
    {
        if ($this->m($word) > 1 && $this->doubleConsonant($word) && mb_substr($word, -1) == 'l') {
            $word = mb_substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Replaces the first string with the second, at the end of the string. If fourth arg is given, then the preceding
     * string must match that m count at least.
     *
     * @param  string $string
     * @param  string $check
     * @param  string $replacement
     * @param  int    $m
     *
     * @return bool
     */
    protected function replace(&$string, $check, $replacement, $m = null)
    {
        $len = 0 - mb_strlen($check);

        if (mb_substr($string, $len) == $check) {
            $substring = mb_substr($string, 0, $len);

            if (is_null($m) || $this->m($substring) > $m) {
                $string = $substring . $replacement;
            }

            return true;
        }

        return false;
    }


    /**
     * Measures the number of consonant sequences in $str. if c is a consonant sequence and v a vowel sequence, and <..>
     * indicates arbitrary presence,
     *
     * <c><v>       gives 0
     * <c>vc<v>     gives 1
     * <c>vcvc<v>   gives 2
     * <c>vcvcvc<v> gives 3
     *
     * @param string $string
     *
     * @return int
     */
    protected function m($string)
    {
        $string = preg_replace("#^" . self::CONSONANT . "+#", '', $string);

        $string = preg_replace("#" . self::VOWEL . "+$#", '', $string);

        preg_match_all("#(" . self::VOWEL . "+" . self::CONSONANT . "#", $string, $matches);

        return count($matches[1]);
    }


    /**
     * Returns true/false as to whether the given string contains two of the same consonant next to each other at the
     * end of the string.
     *
     * @param string $string
     *
     * @return bool
     */
    protected function doubleConsonant($string)
    {
        return preg_match("#" . self::CONSONANT . "{2}$#", $string, $matches) && $matches[0][0] == $matches[0][1];
    }

    /**
     * Checks for ending CVC sequence where second C is not W, X or Y
     *
     * @param string $string
     *
     * @return bool
     */
    protected function cvc($string)
    {
        return preg_match("#(" . self::CONSONANT . self::VOWEL . self::CONSONANT . "$#", $string, $matches)
            && mb_strlen($matches[1]) == 3
            && $matches[1][2] != 'w'
            && $matches[1][2] != 'x'
            && $matches[1][2] != 'y';
    }
}
