<?php

namespace BlixtTests;

use Blixt\Blixt;
use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Index\Schema\Column;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Storage\SQLite\SQLiteStorageFactory;
use Blixt\Tokenization\DefaultTokenizer;

class BlixtTest extends TestCase
{
    /** @test */
    public function testOpenAndDestroyIndex()
    {
        $directory = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        $blixt = new Blixt(
            new SQLiteStorageFactory($directory),
            new EnglishStemmer(),
            new DefaultTokenizer()
        );

        $name = md5(str_random(10));
        $index = $blixt->open($name, function (Schema $schema) {
            $schema->addColumn('name', true, true, 2.6);
            $schema->addColumn(new Column('about'));
        });

        $path = $directory . DIRECTORY_SEPARATOR . $name;
        $this->assertFileExists($path . '.index');

        $index->destroy();
        $this->assertFileNotExists($path . '.index');

//        $this->expectException(IndexDoesNotExistException::class);
//        $blixt->open('non_existent_index');
    }
}