<?php

namespace BlixtTests\Index;

use Blixt\Blixt;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Index\Indexable;
use Blixt\Index\Index;
use Blixt\Index\Schema\Blueprint;
use Blixt\Index\Schema\Definition;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Drivers\Memory\Storage as MemoryStorage;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\DocumentRepository;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Token;
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
    protected $nameColumn;
    protected $ageColumn;

    public function setUp()
    {
        $this->schema = new Schema(1, 'test');
        $this->nameColumn = new Column(1, 1, 'name', true, false);
        $this->ageColumn = new Column(2, 1, 'age', false, true);
        $this->schema->setColumns(new Collection([$this->nameColumn, $this->ageColumn]));

        $this->storage = m::mock(Storage::class);
        $this->stemmer = m::mock(Stemmer::class);
        $this->tokenizer = m::mock(Tokenizer::class);
        $this->blixt = m::mock(Blixt::class);

        $this->index = new Index($this->storage, $this->tokenizer, $this->stemmer, $this->schema);
    }

    public function testIndexingAlreadyExistingDocumentThrowsDocumentAlreadyExistsException()
    {
        $document = new Indexable(1);

        $documentRepo = m::mock(DocumentRepository::class);
        $this->storage->shouldReceive('documents')->once()->andReturn($documentRepo);
        $documentRepo->shouldReceive('findByKey')->once()->withArgs([$document->getKey()])->andReturn($document);

        $this->expectException(DocumentAlreadyExistsException::class);
        $this->index->add($document);
    }

    public function testIndexingDocumentWithMissingFieldsThrowsInvalidDocumentException()
    {
        $document = new Indexable(123);
        $document->setField('name', 'Joe Bloggs');

        $documentRepo = m::mock(DocumentRepository::class);
        $this->storage->shouldReceive('documents')->once()->andReturn($documentRepo);
        $documentRepo->shouldReceive('findByKey')->withArgs([$document->getKey()])->andReturn(null);

        $this->expectException(InvalidDocumentException::class);
        $this->index->add($document);
    }

    /** @dataProvider documentsIndexedCorrectlyProvider */
    public function testDocumentsAreCorrectlyIndexed($blueprint, $indexable, $expected)
    {
        $storage = new MemoryStorage();
        $storage->create();
        $tokenizer = new DummyTokenizer();
        $stemmer = new DummyStemmer();

        $blixt = new Blixt($storage, $stemmer, $tokenizer);
        $index = $blixt->create($blueprint);
        $index->add($indexable);

        $reflection = new \ReflectionClass(MemoryStorage::class);
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);
        $actual = $dataProperty->getValue($storage);

//        var_dump($actual, $expected);die();
        $this->assertEquals($expected, $actual);
    }

    public function documentsIndexedCorrectlyProvider()
    {
        $people = new Blueprint('people', [
            new Definition('name', true, false),
            new Definition('age', false, true)
        ]);

        $joeBloggs = new Indexable(1, [
            'name' => 'Joe Bloggs',
            'age' => 30
        ]);

        $expectedPeopleJoeBloggs = [
            'schemas' => [
                1 => ['name' => 'people']
            ],
            'columns' => [
                1 => ['schema_id' => 1, 'name' => 'name', 'is_indexed' => true, 'is_stored' => false],
                2 => ['schema_id' => 1, 'name' => 'age', 'is_indexed' => false, 'is_stored' => true],
            ],
            'words' => [
                1 => ['word' => 'joe'],
                2 => ['word' => 'bloggs']
            ],
            'terms' => [
                1 => ['schema_id' => 1, 'word_id' => 1, 'field_count' => 1],
                2 => ['schema_id' => 1, 'word_id' => 2, 'field_count' => 1]
            ],
            'fields' => [
                1 => ['document_id' => 1, 'column_id' => 1, 'value' => null],
                2 => ['document_id' => 1, 'column_id' => 2, 'value' => 30],
            ],
            'occurrences' => [
                1 => ['field_id' => 1, 'term_id' => 1, 'frequency' => 1],
                2 => ['field_id' => 1, 'term_id' => 2, 'frequency' => 1],
            ],
            'positions' => [
                1 => ['occurrence_id' => 1, 'position' => 0],
                1 => ['occurrence_id' => 2, 'position' => 1],
            ]
        ];

        return [
            [$people, $joeBloggs, $expectedPeopleJoeBloggs]
        ];
    }

//    public function testAddIndexesDocumentCorrectly()
//    {
//        $indexable = new Indexable(123);
//        $indexable->setField('name', 'Joe Bloggs');
//        $indexable->setField('age', 30);
//
//        $inputDocument = new Document(null, $this->schema->getId(), $indexable->getKey());
//        $document = new Document(1, $this->schema->getId(), $indexable->getKey());
//        $inputNameField = new Field(null, 1, 1, null);  // Name should be indexed, but not stored.
//        $nameField = new Field(1, 1, 1, null);
//        $inputAgeField = new Field(null, 1, 2, 30);     // Age should be stored, but not indexed.
//        $ageField = new Field(2, 1, 2, 30);
//
//        $documentRepo = m::mock(DocumentRepository::class);
//        $this->storage->shouldReceive('documents')->andReturn($documentRepo);
//        $documentRepo->shouldReceive('findByKey')->withArgs([$indexable->getKey()])->andReturn(null);
//        $documentRepo->shouldReceive('save')->with(m::on(function ($arg) use ($inputDocument) {
//            return $inputDocument == $arg;
//        }))->andReturn($document);
//
//        $fieldsRepo = m::mock(FieldRepository::class);
//        $this->storage->shouldReceive('fields')->andReturn($fieldsRepo);
//        $fieldsRepo->shouldReceive('save')->with(m::on(function ($arg) use ($inputNameField) {
//            return $inputNameField == $arg;
//        }))->andReturn($nameField);
//        $fieldsRepo->shouldReceive('save')->with(m::on(function ($arg) use ($inputAgeField) {
//            return $inputAgeField == $arg;
//        }))->andReturn($ageField);
//
//        $joeToken = new Token('joe', 0);
//        $bloggsToken = new Token('bloggs', 1);
//        $tokens = new Collection([$joeToken, $bloggsToken]);
//        $this->tokenizer->shouldReceive('tokenize')->with('Joe Bloggs')->andReturn($tokens);
//        $this->stemmer->shouldReceive('stem')->with($joeToken->getText())->andReturn('joe');
//        $this->stemmer->shouldReceive('stem')->with($bloggsToken->getText())->andReturn('blogg');
//
//        $inputJoeWord = new Word(null, 'joe');
//        $joeWord = new Word(1, 'joe');
//        $inputBloggWord = new Word(null, 'blogg');
//        $bloggWord = new Word(1, 'blogg');
//        $wordsRepo = m::mock(WordRepository::class);
//        $this->storage->shouldReceive('words')->andReturn($wordsRepo);
//        $wordsRepo->shouldReceive('findByWord')->with('joe')->andReturn(null);
//        $wordsRepo->shouldReceive('save')->with(m::on(function ($arg) use ($inputJoeWord) {
//            return $arg == $inputJoeWord;
//        }))->andReturn($joeWord);
//        $wordsRepo->shouldReceive('findByWord')->with('blogg')->andReturn(null);
//        $wordsRepo->shouldReceive('save')->with(m::on(function ($arg) use ($inputBloggWord) {
//            return $arg == $inputBloggWord;
//        }))->andReturn($bloggWord);
//
//        $inputJoeTerm = new Term(null, $this->schema->getId(), $joeWord->getId(), 0);
//        $joeTerm = new Term(1, $this->schema->getId(), $joeWord->getId(), 0);
//        $inputBloggTerm = new Term(null, $this->schema->getId(), $bloggWord->getId(), 0);
//        $bloggTerm = new Term(1, $this->schema->getId(), $bloggWord->getId(), 0);
//        $termRepo = m::mock(TermRepository::class);
//        $this->storage->shouldReceive('terms')->andReturn($termRepo);
//        $termRepo->shouldReceive('findBySchemaAndWord')->with($this->schema, $joeWord)->andReturn(null);
//        $termRepo->shouldReceive('save')->with(m::on(function ($arg) use ($inputJoeTerm) {
//            return $arg == $inputJoeTerm;
//        }))->andReturn($joeTerm);
//        $termRepo->shouldReceive('findBySchemaAndWord')->with($this->schema, $bloggWord)->andReturn(null);
//        $termRepo->shouldReceive('save')->with(m::on(function ($arg) use ($inputBloggTerm) {
//            return $arg == $inputBloggTerm;
//        }))->andReturn($bloggTerm);
//
//
//
//        $this->index->add($indexable);
//    }
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
        $tokens = new Collection();

        $i = 0;
        foreach (explode(' ', mb_strtolower(trim($text))) as $word) {
            $tokens->push(new Token($word, $i++));
        }

        return $tokens;
    }
}