<?php

class BlixtTest extends TestCase
{
    /** @test */
    public function testSomething()
    {
        $tokenizer = new \Blixt\Tokenization\Tokenizers\DefaultTokenizer();
        var_dump($tokenizer->tokenize('this is a test string'));
    }
}