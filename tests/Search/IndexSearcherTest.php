<?php

namespace BlixtTests\Search;

use Blixt\Blixt;
use Blixt\Blueprint\Blueprint;
use Blixt\Document\Indexable;
use Blixt\Persistence\Drivers\MemoryStorage;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Tokenization\DefaultTokenizer;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class IndexSearcherTest extends TestCase
{
    public function setUp()
    {
    }

    /**
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testSomething()
    {
        $this->markTestSkipped('In progress');
        $blixt = new Blixt(new MemoryStorage(), new DefaultTokenizer(), new EnglishStemmer());
        $blixt->install();
        $index = $blixt->open('test', function (Blueprint $blueprint) {
            $blueprint->createDefinition('name', true, false);
            $blueprint->createDefinition('age', false, true);
        });
        $index->add(new Indexable(1, Collection::make([
            'name' => 'Joe Bloggs',
            'age' => 30
        ])));
        $index->add(new Indexable(2, Collection::make([
            'name' => 'Jane Doe',
            'age' => 25
        ])));
        $index->search('jane');
    }
}
