<?php

class BlixtTest extends TestCase
{
    /** @test */
    public function testSomething()
    {
        $blixt = new \Blixt\Blixt(
            new \Blixt\Storage\Connectors\SQLiteConnector(__DIR__),
            new \Blixt\Stemming\EnglishStemmer(),
            new \Blixt\Tokenization\DefaultTokenizer()
        );

        $index = $blixt->open('test.index');
        var_dump($blixt, $index);die();
    }
}