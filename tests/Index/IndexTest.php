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

    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    public function setUp()
    {
        $this->storage = new Storage();
        $stemmer = new EnglishStemmer();
        $tokenizer = new DefaultTokenizer();

        Blixt::install($this->storage);

        $this->blixt = new Blixt($this->storage, $stemmer, $tokenizer);
        $this->index = $this->blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addDefinition('name', true, false);
            $blueprint->addDefinition('age', false, true);
        });
    }

    public function testSomething()
    {
        Blixt::install($storage = new Storage());

        $blixt = new Blixt($storage, new EnglishStemmer(), new DefaultTokenizer());
        $index = $blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addDefinition('name', true, false);
            $blueprint->addDefinition('age', false, true);
        });

        // Use reflection to make the data property visible
        // Ensure that a schema has been created, with 2 columns matching the above

        $document = new Document(1, [
            'name' => 'Joe Bloggs',
            'age' => 29
        ]);

        $index->add($document);

        // Ensure that the document has been correctly added to the index in that:
        // - a document record was added
        // - two field records were added (name field should be indexed, but not stored and age should be stored but not indexed)
        // - two word records should be present (joe, blogg) along with two term records representing each word in the schema
        // - two occurrence records representing each word in the name field should be present
        // - two position records, one for each term in the field should be present (joe - 0, blogg - 1)
    }

    public function testIndexingAlreadyExistingDocumentThrowsException() {}
    public function testIndexingDocumentWithMissingFieldsThrowsException() {}
}