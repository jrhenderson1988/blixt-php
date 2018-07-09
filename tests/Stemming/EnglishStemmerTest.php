<?php

namespace BlixtTests\Stemming;

use Blixt\Stemming\EnglishStemmer;
use BlixtTests\TestCase;

class EnglishStemmerTest extends TestCase
{
    /**
     * @var \Blixt\Stemming\EnglishStemmer
     */
    protected $stemmer;

    public function setUp()
    {
        $this->stemmer = new EnglishStemmer();
    }

    /**
     * @test
     * @dataProvider getWordsData
     * @param string $input
     * @param string $expected
     */
    public function testStemmingWords(string $input, string $expected)
    {
        $this->assertEquals($expected, $this->stemmer->stem($input));
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
