<?php

namespace BlixtTests\Index;

use Blixt\Blixt;
use Blixt\Index\Document\Document;
use Blixt\Index\Schema\Blueprint;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Tokenization\DefaultTokenizer;
use BlixtTests\TestCase;

class IndexTest extends TestCase
{
    /**
     * @var \Blixt\Blixt
     */
    protected $blixt;

    /**
     * @var \Blixt\Index\Index
     */
    protected $index;

    public function setUp()
    {
        $storage = new Storage();
        $stemmer = new EnglishStemmer();
        $tokenizer = new DefaultTokenizer();

        Blixt::install($storage);

        $this->blixt = new Blixt($storage, $stemmer, $tokenizer);
        $this->index = $this->blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addDefinition('name', true, false);
            $blueprint->addDefinition('age', false, true);
        });
    }

    public function testSomething()
    {
        $document = new Document(1, [
            'name' => 'Joe Bloggs',
            'age' => 29
        ]);

        $this->index->add($document);
    }

    public function testIndexingAlreadyExistingDocumentThrowsException() {}
    public function testIndexingDocumentWithMissingFieldsThrowsException() {}
}