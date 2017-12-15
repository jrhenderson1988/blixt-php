<?php

namespace BlixtTests;

use Blixt\Blixt;
use Blixt\Documents\Document;
use Blixt\Documents\Field;
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

//        $name = md5(str_random(10));
        $name = 'test';
        $index = $blixt->open($name, function (Schema $schema) {
            $schema->addColumn('name', true, true, 2.6);
            $schema->addColumn(new Column('about'));
        });

        $path = $directory . DIRECTORY_SEPARATOR . $name;
        $this->assertFileExists($path . '.index');

        $document = new Document(1);
        $document->addField(new Field('name', 'Jonathon Henderson'));
        $document->addField(new Field('about', 'He is pretty awesome. He is absolutely amazing.'));
        $index->add($document);

//        $index->destroy();
//        $this->assertFileNotExists($path . '.index');

//        $this->expectException(IndexDoesNotExistException::class);
//        $blixt->open('non_existent_index');
    }
}