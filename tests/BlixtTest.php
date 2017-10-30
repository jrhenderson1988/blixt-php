<?php

namespace BlixtTests;

use Blixt\Blixt;
use Blixt\Documents\Document;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Storage\SQLite\Factory;
use Blixt\Tokenization\DefaultTokenizer;

class BlixtTest extends TestCase
{
    /** @test */
    public function testOpenAndDestroyIndex()
    {
        $directory = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        $filename = md5(str_random(10)) . '.index';
        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        $blixt = new Blixt(
            new Factory($directory),
            new EnglishStemmer(),
            new DefaultTokenizer()
        );

        $index = $blixt->open($filename);
        $this->assertFileExists($path);

        $index->addDocument('users', new Document('test', []));

        $index->destroy();
        $this->assertFileNotExists($path);
    }
}