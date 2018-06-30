<?php

namespace BlixtTests\Index;

use Blixt\Blixt;
use Blixt\Document\Indexable;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Index\Index;
use Blixt\Persistence\Drivers\Driver as StorageDriver;
use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Document;
use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Occurrence;
use Blixt\Persistence\Entities\Position;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\ColumnRepository;
use Blixt\Persistence\Repositories\DocumentRepository;
use Blixt\Persistence\Repositories\FieldRepository;
use Blixt\Persistence\Repositories\SchemaRepository;
use Blixt\Persistence\Repositories\TermRepository;
use Blixt\Persistence\Repositories\WordRepository;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Word;
use Blixt\Tokenization\Token;
use Blixt\Tokenization\Tokenizer;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use Mockery as m;

// TODO: Move testDocumentsAreCorrectlyIndexed into a memory driver based test
// Test that fields marked as not stored are not stored
// Test that fields marked as not indexed don't have occurrence and position records created
// Test that fields marked as stored have a value in their field
// Test that fields marked as indexed have position and occurrence records

class IndexTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    protected $storage;

    /**
     * @var \Mockery\MockInterface
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Blixt
     */
    protected $blixt;

    /**
     * @var \Blixt\Index\Index
     */
    protected $index;

    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $schema;

    /**
     * @var \Blixt\Persistence\Entities\Column
     */
    protected $nameColumn;

    /**
     * @var \Blixt\Persistence\Entities\Column
     */
    protected $ageColumn;

    public function setUp()
    {
        $this->blixt = new Blixt(
            $this->storage = m::mock(StorageDriver::class),
            $this->tokenizer = m::mock(Tokenizer::class)
        );
    }

    /**
     * @param \Blixt\Persistence\Entities\Schema $schema
     *
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function makeIndexForSchema(Schema $schema)
    {
        $this->schema = $schema;

        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $schema->getName()]])
            ->andReturn(new Record($schema->getId(), [SchemaRepository::NAME => $schema->getName()]));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([ColumnRepository::TABLE, [ColumnRepository::SCHEMA_ID => $schema->getId()], 0, null])
            ->andReturn(
                $schema->getColumns()->map(function (Column $column) {
                    return new Record($column->getId(), [
                        ColumnRepository::SCHEMA_ID => $column->getSchemaId(),
                        ColumnRepository::NAME => $column->getName(),
                        ColumnRepository::IS_INDEXED => $column->isIndexed(),
                        ColumnRepository::IS_STORED => $column->isStored()
                    ]);
                })->toArray()
            );

        return $this->index = $this->blixt->open($schema->getName());
    }

    /**
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function makeIndexForPeopleSchemaWithNameAndAgeColumns()
    {
        $this->schema = Schema::make(1, 'people');

        $this->schema->setColumns(Collection::make([
            $this->nameColumn = Column::make(1, 1, 'name', true, false),
            $this->ageColumn = Column::make(2, 1, 'age', false, true),
        ]));

        return $this->index = $this->makeIndexForSchema($this->schema);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testIndexingAlreadyExistingDocumentThrowsDocumentAlreadyExistsException()
    {
        $this->makeIndexForPeopleSchemaWithNameAndAgeColumns();

        $indexable = new Indexable(1);

        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([DocumentRepository::TABLE, [DocumentRepository::KEY => $indexable->getKey()]])
            ->andReturn(new Record($indexable->getKey(), [
                DocumentRepository::SCHEMA_ID => $this->schema->getId(),
                DocumentRepository::KEY => $indexable->getKey()
            ]));

        $this->expectException(DocumentAlreadyExistsException::class);
        $this->index->add($indexable);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testIndexingDocumentWithMissingFieldsThrowsInvalidDocumentException()
    {
        $this->makeIndexForPeopleSchemaWithNameAndAgeColumns();

        $indexable = new Indexable(123);
        $indexable->setField('name', 'Joe Bloggs');

        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([DocumentRepository::TABLE, [DocumentRepository::KEY => $indexable->getKey()]])
            ->andReturnNull();

        $this->expectException(InvalidDocumentException::class);
        $this->index->add($indexable);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testExtraFieldsAreSilentlyIgnoredWhenIndexingDocument()
    {
        $this->makeIndexForPeopleSchemaWithNameAndAgeColumns();

        $name = 'Joe Bloggs';
        $age = 30;

        $document = Document::make(1, $this->schema->getId(), 123);
        $nameField = Field::make(1, $document->getId(), $this->nameColumn->getId(), null);
        $ageFieldId = Field::make(2, $document->getId(), $this->ageColumn->getId(), 30);
        $wordJoe = Word::make(1, 'joe');
        $wordBloggs = Word::make(2, 'bloggs');
        $termJoe = Term::make(1, $this->schema->getId(), $wordJoe->getId(), 1);
        $termBloggs = Term::make(2, $this->schema->getId(), $wordBloggs->getId(), 1);
        $occurrenceJoe = Occurrence::make(1, $nameField->getId(), $termJoe->getId(), 1);
        $occurrenceBloggs = Occurrence::make(2, $nameField->getId(), $termBloggs->getId(), 1);
        $positionJoe = Position::make(1, $occurrenceJoe->getId(), 0);
        $positionBloggs = Position::make(1, $occurrenceJoe->getId(), 1);

        $indexable = new Indexable(123);
        $indexable->setField('name', $name);
        $indexable->setField('age', $age);
        $indexable->setField('extra', 'This should be ignored.');

        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([DocumentRepository::TABLE, [DocumentRepository::KEY => $indexable->getKey()]])
            ->andReturnNull();

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([DocumentRepository::TABLE, [
                DocumentRepository::SCHEMA_ID => $this->schema->getId(),
                DocumentRepository::KEY => $indexable->getKey()
            ]])
            ->andReturn(new Record($document->getId(), [
                DocumentRepository::SCHEMA_ID => $document->getSchemaId(),
                DocumentRepository::KEY => $document->getKey()
            ]));

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FieldRepository::TABLE, [
                FieldRepository::DOCUMENT_ID => $nameField->getDocumentId(),
                FieldRepository::COLUMN_ID => $nameField->getColumnId(),
                FieldRepository::VALUE => $nameField->getValue()
            ]])
            ->andReturn(new Record($nameField->getId(), [
                FieldRepository::DOCUMENT_ID => $nameField->getDocumentId(),
                FieldRepository::COLUMN_ID => $nameField->getColumnId(),
                FieldRepository::VALUE => $nameField->getValue()
            ]));

        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs([$name])
            ->andReturn(Collection::make([
                new Token($wordJoe->getWord(), 0), new Token($wordBloggs->getWord(), 1)
            ]));

        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([WordRepository::TABLE, [WordRepository::WORD => $wordJoe->getWord()]])
            ->andReturnNull();

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([WordRepository::TABLE, [WordRepository::WORD => $wordJoe->getWord()]])
            ->andReturn(new Record($wordJoe->getId(), [
                WordRepository::WORD => $wordJoe->getWord()
            ]));

        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([TermRepository::TABLE, [
                TermRepository::SCHEMA_ID => $termJoe->getSchemaId(),
                TermRepository::WORD_ID => $termJoe->getWordId()
            ]])
            ->andReturnNull();

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TermRepository::TABLE, [
                TermRepository::SCHEMA_ID => $termJoe->getSchemaId(),
                TermRepository::WORD_ID => $termJoe->getWordId(),
                TermRepository::FIELD_COUNT => $termJoe->getFieldCount()
            ]])
            ->andReturn(new Record($termJoe->getId(), [
                TermRepository::SCHEMA_ID => $termJoe->getSchemaId(),
                TermRepository::WORD_ID => $termJoe->getWordId()
            ]));



        $this->index->add($indexable);









//        $schema = Schema::make(1, 'test');
//        $schema->setColumns(Collection::make([
//            $nameColumn = Column::make(1, 1, 'name', false, true),
//            $ageColumn = Column::make(2, 1, 'age', false, true)
//        ]));
//        $this->makeIndexForSchema($schema);
//        $indexable = new Indexable(123);
//        $indexable->setField('name', 'Joe Bloggs');
//        $indexable->setField('age', 29);
//        $indexable->setField('extra', 'this should be ignored.');
//        $documentRepo = m::mock(DocumentRepository::class);
//        $this->storage->shouldReceive('documents')->andReturn($documentRepo);
//        $documentRepo->shouldReceive('findByKey')->withArgs([$indexable->getKey()])->andReturn(null);
//        $documentRepo->shouldReceive('save')->with(m::on(function ($arg) use ($schema, $indexable) {
//            return $arg == Document::create($schema->getId(), $indexable->getKey());
//        }))->andReturn($document = Document::make(1, $schema->getId(), $indexable->getKey()));
//        $fieldRepo = m::mock(FieldRepository::class);
//        $this->storage->shouldReceive('fields')->andReturn($fieldRepo);
//        $fieldRepo->shouldReceive('save')->twice()->with(m::on(function ($arg) use ($document, $nameColumn, $ageColumn) {
//            if ($arg instanceof Field && $arg->getDocumentId() == $document->getId()) {
//                if ($arg->getColumnId() == $nameColumn->getId()) {
//                    return $arg->getValue() == 'Joe Bloggs';
//                } elseif ($arg->getColumnId() == $ageColumn->getId()) {
//                    return $arg->getValue() == 29;
//                }
//            }
//            return false;
//        }))->andReturn(Field::make(1, $document->getId(), $nameColumn->getId(), null));
//        $this->assertTrue($this->index->add($indexable));
    }

//    /**
//     * @dataProvider documentsIndexedCorrectlyProvider
//     * @test
//     */
//    public function testDocumentsAreCorrectlyIndexed($blueprint, $indexables, $expected)
//    {
//        $storage = new MemoryStorage();
//        $storage->create();
//        $blixt = new Blixt($storage, $tokenizer = new DummyTokenizer(new DummyStemmer()));
//        $index = $blixt->create($blueprint);
//        $indexables = is_array($indexables) ? $indexables : [$indexables];
//        foreach ($indexables as $indexable) {
//            $index->add($indexable);
//        }
//        $data = $this->getInaccessibleProperty($storage, 'data');
//        $this->assertEquals($expected, $data);
//    }

//    public function documentsIndexedCorrectlyProvider()
//    {
//        $peopleBlueprint = new Blueprint('people', Collection::make([
//            new Definition('name', true, false),
//            new Definition('age', false, true)
//        ]));
//
//        $joeBloggsIndexable = new Indexable(1, Collection::make([
//            'name' => 'Joe Bloggs',
//            'age' => 30
//        ]));
//
//        $janeDoeIndexable = new Indexable(2, Collection::make([
//            'name' => 'Jane Doe',
//            'age' => 28
//        ]));
//
//        $expectedPeopleJoeBloggs = [
//            'schemas' => [
//                1 => ['name' => 'people']
//            ],
//            'columns' => [
//                1 => ['schema_id' => 1, 'name' => 'name', 'is_indexed' => true, 'is_stored' => false],
//                2 => ['schema_id' => 1, 'name' => 'age', 'is_indexed' => false, 'is_stored' => true]
//            ],
//            'words' => [
//                1 => ['word' => 'joe'],
//                2 => ['word' => 'bloggs']
//            ],
//            'terms' => [
//                1 => ['schema_id' => 1, 'word_id' => 1, 'field_count' => 1],
//                2 => ['schema_id' => 1, 'word_id' => 2, 'field_count' => 1]
//            ],
//            'documents' => [
//                1 => ['schema_id' => 1, 'key' => 1]
//            ],
//            'fields' => [
//                1 => ['document_id' => 1, 'column_id' => 1, 'value' => null],
//                2 => ['document_id' => 1, 'column_id' => 2, 'value' => 30]
//            ],
//            'occurrences' => [
//                1 => ['field_id' => 1, 'term_id' => 1, 'frequency' => 1],
//                2 => ['field_id' => 1, 'term_id' => 2, 'frequency' => 1]
//            ],
//            'positions' => [
//                1 => ['occurrence_id' => 1, 'position' => 0],
//                2 => ['occurrence_id' => 2, 'position' => 1]
//            ]
//        ];
//
//        $expectedPeopleJoeBloggsJaneDoe = [
//            'schemas' => [
//                1 => ['name' => 'people']
//            ],
//            'columns' => [
//                1 => ['schema_id' => 1, 'name' => 'name', 'is_indexed' => true, 'is_stored' => false],
//                2 => ['schema_id' => 1, 'name' => 'age', 'is_indexed' => false, 'is_stored' => true]
//            ],
//            'words' => [
//                1 => ['word' => 'joe'],
//                2 => ['word' => 'bloggs'],
//                3 => ['word' => 'jane'],
//                4 => ['word' => 'doe']
//            ],
//            'terms' => [
//                1 => ['schema_id' => 1, 'word_id' => 1, 'field_count' => 1],
//                2 => ['schema_id' => 1, 'word_id' => 2, 'field_count' => 1],
//                3 => ['schema_id' => 1, 'word_id' => 3, 'field_count' => 1],
//                4 => ['schema_id' => 1, 'word_id' => 4, 'field_count' => 1]
//            ],
//            'documents' => [
//                1 => ['schema_id' => 1, 'key' => 1],
//                2 => ['schema_id' => 1, 'key' => 2]
//            ],
//            'fields' => [
//                1 => ['document_id' => 1, 'column_id' => 1, 'value' => null],
//                2 => ['document_id' => 1, 'column_id' => 2, 'value' => 30],
//                3 => ['document_id' => 2, 'column_id' => 1, 'value' => null],
//                4 => ['document_id' => 2, 'column_id' => 2, 'value' => 28]
//            ],
//            'occurrences' => [
//                1 => ['field_id' => 1, 'term_id' => 1, 'frequency' => 1],
//                2 => ['field_id' => 1, 'term_id' => 2, 'frequency' => 1],
//                3 => ['field_id' => 3, 'term_id' => 3, 'frequency' => 1],
//                4 => ['field_id' => 3, 'term_id' => 4, 'frequency' => 1]
//            ],
//            'positions' => [
//                1 => ['occurrence_id' => 1, 'position' => 0],
//                2 => ['occurrence_id' => 2, 'position' => 1],
//                3 => ['occurrence_id' => 3, 'position' => 0],
//                4 => ['occurrence_id' => 4, 'position' => 1]
//            ]
//        ];
//
//        return [
//            'people schema, joe bloggs' => [
//                $peopleBlueprint, [$joeBloggsIndexable], $expectedPeopleJoeBloggs
//            ],
//            'people schema, joe bloggs and jane doe' => [
//                $peopleBlueprint, [$joeBloggsIndexable, $janeDoeIndexable], $expectedPeopleJoeBloggsJaneDoe
//            ],
//        ];
//    }

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
    public function stem(string $word): string
    {
        return $word;
    }
}

class DummyTokenizer implements Tokenizer
{
    protected $stemmer;

    public function __construct(Stemmer $stemmer)
    {
        $this->stemmer = $stemmer;
    }

    public function tokenize(string $text): Collection
    {
        $tokens = new Collection();

        $i = 0;
        foreach (explode(' ', mb_strtolower(trim($text))) as $word) {
            $tokens->push(new Token($this->stemmer->stem($word), $i++));
        }

        return $tokens;
    }
}