<?php

namespace BlixtTests;

class BlixtTest extends TestCase
{
    /** @test */
    public function testOpenAndDestroyIndex()
    {
        $directory = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        $filename = md5(str_random(10)) . '.index';
        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        $blixt = new \Blixt\Blixt(
            new \Blixt\Storage\SQLite\Factory($directory),
            new \Blixt\Stemming\EnglishStemmer(),
            new \Blixt\Tokenization\DefaultTokenizer()
        );

        $index = $blixt->open($filename);
        $this->assertFileExists($path);

        $index->destroy();
        $this->assertFileNotExists($path);
    }
}