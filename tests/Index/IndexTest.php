<?php

namespace BlixtTests\Index;

use Blixt\Blixt;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Index\Indexable;
use Blixt\Index\Index;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\DocumentRepository;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use Mockery as m;

class IndexTest extends TestCase
{
    protected $storage;
    protected $stemmer;
    protected $tokenizer;
    protected $blixt;
    protected $index;
    protected $schema;

    public function setUp()
    {
        $this->schema = new Schema(1, 'test');
        $this->schema->setColumns(new Collection([
            new Column(1, 1, 'name', true, false),
            new Column(1, 1, 'age', false, true)
        ]));
        $this->storage = m::mock(Storage::class);
        $this->stemmer = m::mock(Stemmer::class);
        $this->tokenizer = m::mock(Stemmer::class);
        $this->blixt = m::mock(Blixt::class);
        $this->blixt->shouldReceive('getStorage')->once()->andReturn($this->storage);
        $this->blixt->shouldReceive('getStemmer')->once()->andReturn($this->stemmer);
        $this->blixt->shouldReceive('getTokenizer')->once()->andReturn($this->tokenizer);
        $this->index = new Index($this->blixt, $this->schema);
    }

    public function testDocumentAlreadyExistsExceptionIsThrownFromAddMethodWhenDocumentExistsInSchema()
    {
        $document = new Indexable(1);

        $documentRepo = m::mock(DocumentRepository::class);
        $this->storage->shouldReceive('documents')->once()->andReturn($documentRepo);
        $documentRepo->shouldReceive('findByKey')->once()->withArgs([$document->getKey()])->andReturn($document);

        $this->expectException(DocumentAlreadyExistsException::class);
        $this->index->add($document);
    }

    public function testInvalidDocumentExceptionIsThrownWhenDocumentOmitsRequiredField()
    {
        $document = new Indexable(123);
        $document->setField('name', 'Joe Bloggs');

        $documentRepo = m::mock(DocumentRepository::class);
        $this->storage->shouldReceive('documents')->once()->andReturn($documentRepo);
        $documentRepo->shouldReceive('findByKey')->withArgs([$document->getKey()])->andReturn(null);

        $this->expectException(InvalidDocumentException::class);
        $this->index->add($document);
    }

    public function testAddIndexesDocumentCorrectly()
    {

    }

//    public function testCreateMethodCreatesSchemaAndColumns()
//    {
//        Blixt::install($storage = new Storage());
//
//        $blixt = new Blixt($storage, new DummyStemmer(), new DummyTokenizer());
//        $index = $blixt->create('test', function (Blueprint $blueprint) {
//            $blueprint->addDefinition('name', true, false);
//            $blueprint->addDefinition('age', false, true);
//        });
//
//
//    }

//    public function testSomething()
//    {
//        Blixt::install($storage = new Storage());
//
//        $blixt = new Blixt($storage, new DummyStemmer(), new DummyTokenizer());
//        $index = $blixt->create('test', function (Blueprint $blueprint) {
//            $blueprint->addDefinition('name', true, false);
//            $blueprint->addDefinition('age', false, true);
//        });
//
//        // Use reflection to make the data property visible
//        // Ensure that a schema has been created, with 2 columns matching the above
//
//        $document = new Document(1, [
//            'name' => 'Joe Bloggs',
//            'age' => 29
//        ]);
//
//        $index->add($document);
//
//        // Ensure that the document has been correctly added to the index in that:
//        // - a document record was added
//        // - two field records were added (name field should be indexed, but not stored and age should be stored but not indexed)
//        // - two word records should be present (joe, blogg) along with two term records representing each word in the schema
//        // - two occurrence records representing each word in the name field should be present
//        // - two position records, one for each term in the field should be present (joe - 0, blogg - 1)
//    }

    public function testIndexingAlreadyExistingDocumentThrowsException() {}
    public function testIndexingDocumentWithMissingFieldsThrowsException() {}
}

class DummyStemmer implements Stemmer
{
    public function stem($word)
    {
        return $word;
    }
}

class DummyTokenizer implements Tokenizer
{
    public function tokenize($text)
    {
        return explode(' ', mb_strtolower(trim($text)));
    }
}