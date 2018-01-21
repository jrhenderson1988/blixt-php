<?php

namespace BlixtTests\Stemming;

use Blixt\Stemming\EnglishStemmer;
use BlixtTests\TestCase;

class EnglishStemmerTest extends TestCase
{
    /** @test  */
    public function testStemmingWords()
    {
        $words = [
            'writing' => 'write',
            'and' => 'and',
            'running' => 'run',
            'tests' => 'test',
            'is' => 'is',
            'incredibly' => 'incred',
            'boring' => 'bore'
        ];

        $stemmer = new EnglishStemmer();
        foreach ($words as $word => $expected) {
            $this->assertEquals($expected, $stemmer->stem($word));
        }
    }
}