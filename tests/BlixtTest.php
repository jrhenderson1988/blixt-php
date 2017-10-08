<?php

class BlixtTest extends TestCase
{
    /** @test */
    public function testSomething()
    {
        $blixt = new \Blixt\Blixt(
            new \Blixt\Storage\SQLite\Factory(__DIR__),
            new \Blixt\Stemming\EnglishStemmer(),
            new \Blixt\Tokenization\DefaultTokenizer()
        );

        $index = $blixt->open('test.index');
        var_dump($blixt, $index);
        $index->destroy();
    }
}