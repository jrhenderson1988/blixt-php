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
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testSomething()
    {
//        $this->markTestSkipped('In progress');
        $blixt = new Blixt(new MemoryStorage(), new DefaultTokenizer(), new EnglishStemmer());
        $blixt->install();

        $people = $blixt->open('people', function (Blueprint $blueprint) {
            $blueprint->createDefinition('name', true, false);
            $blueprint->createDefinition('age', false, true);
        });
        $people->add(new Indexable(1, Collection::make([
            'name' => 'Joe Bloggs',
            'age' => 30
        ])));
        $people->add(new Indexable(2, Collection::make([
            'name' => 'Jane Doe',
            'age' => 25
        ])));

        $dogs = $blixt->open('dogs', function (Blueprint $blueprint) {
            $blueprint->createDefinition('name', true, false);
        });
        $dogs->add(new Indexable(1, Collection::make([
            'name' => 'Rover'
        ])));
        $dogs->add(new Indexable(2, Collection::make([
            'name' => 'Fido'
        ])));


        $people->search('jane +doe -joe fido');
    }
}
