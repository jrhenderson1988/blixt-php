<?php

class BlixtTest extends TestCase
{
    /** @test */
    public function testSomething()
    {
        $blixt = new \Blixt\Blixt(
            new \Blixt\Storage\SQLite\SQLiteStorageFactory(__DIR__),
            new \Blixt\Stemming\EnglishStemmer(),
            new \Blixt\Tokenization\DefaultTokenizer()
        );

        $blixt->open('test.index');
        var_dump($blixt);die();
    }
}