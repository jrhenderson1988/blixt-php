<?php

namespace BlixtTests\Stemming;

use Blixt\Stemming\EnglishStemmer;
use BlixtTests\TestCase;

class EnglishStemmerTest extends TestCase
{
    public function getStemmer()
    {
        return new EnglishStemmer();
    }

    /**
     * @test
     * @dataProvider getWordsData
     */
    public function testStemmingWords($input, $expected)
    {
        $stemmer = $this->getStemmer();
        $this->assertEquals($expected, $stemmer->stem($input));
    }

    public function getWordsData()
    {
        return [
            ['writing', 'write'],
            ['and', 'and'],
            ['running', 'run'],
            ['tests', 'test'],
            ['is', 'is'],
            ['incredibly', 'incred'],
            ['boring', 'bore']
        ];
    }
}