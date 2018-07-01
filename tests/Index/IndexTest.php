<?php

namespace BlixtTests\Index;

use Blixt\Blixt;
use Blixt\Document\Indexable;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Persistence\Drivers\Driver as StorageDriver;
use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\ColumnRepository;
use Blixt\Persistence\Repositories\DocumentRepository;
use Blixt\Persistence\Repositories\FieldRepository;
use Blixt\Persistence\Repositories\OccurrenceRepository;
use Blixt\Persistence\Repositories\PositionRepository;
use Blixt\Persistence\Repositories\SchemaRepository;
use Blixt\Persistence\Repositories\TermRepository;
use Blixt\Persistence\Repositories\WordRepository;
use Blixt\Stemming\Stemmer;
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

    protected function shouldReceiveOnce($method, $args, $return)
    {
        $expectation = $this->storage->shouldReceive($method)
            ->once()
            ->withArgs($args);

        if ($return === null) {
            $expectation->andReturnNull();
        } else {
            $expectation->andReturn($return);
        }

        return $expectation;
    }

    protected function storageShouldReceive($method, $table, $attributes, $andReturn)
    {
        $this->storage->shouldReceive($method)
            ->once()
            ->withArgs([$table, $attributes])
            ->andReturn($andReturn);
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
            ->withArgs([DocumentRepository::TABLE, [
                DocumentRepository::SCHEMA_ID => $this->schema->getId(),
                DocumentRepository::KEY => $indexable->getKey()
            ]])
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
            ->withArgs([DocumentRepository::TABLE, [
                DocumentRepository::SCHEMA_ID => $this->schema->getId(),
                DocumentRepository::KEY => $indexable->getKey()
            ]])
            ->andReturnNull();

        $this->expectException(InvalidDocumentException::class);
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
    public function testDocumentCanBeIndexed()
    {
        $this->makeIndexForPeopleSchemaWithNameAndAgeColumns();

        // Find document by its schema ID and key, returns null (doesn't exist). Create new document.
        $documentCriteria = [DocumentRepository::SCHEMA_ID => $this->schema->getId(), DocumentRepository::KEY => 123];
        $documentAttrs = $documentCriteria;
        $documentRecord = new Record(1, $documentAttrs);
        $this->storageShouldReceive('findBy', DocumentRepository::TABLE, $documentCriteria, null);
        $this->storageShouldReceive('create', DocumentRepository::TABLE, $documentAttrs, $documentRecord);

        // Make field for name (Value is NOT stored)
        $nameFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->nameColumn->getId(),
            FieldRepository::VALUE => null
        ];
        $nameFieldRecord = new Record(1, $nameFieldAttrs);
        $this->storageShouldReceive('create', FieldRepository::TABLE, $nameFieldAttrs, $nameFieldRecord);

        // Tokenize name field
        $tokens = Collection::make([
            $joeToken = new Token('joe', 0),
            $bloggsToken = new Token('bloggs', 1)
        ]);
        $this->tokenizer->shouldReceive('tokenize')->once()->withArgs(['Joe Bloggs'])->andReturn($tokens);

        // Make word for 'joe' in name
        $joeWordCriteria = [WordRepository::WORD => 'joe'];
        $joeWordAttrs = $joeWordCriteria;
        $joeWordRecord = new Record(1, $joeWordAttrs);
        $this->storageShouldReceive('findBy', WordRepository::TABLE, $joeWordCriteria, null);
        $this->storageShouldReceive('create', WordRepository::TABLE, $joeWordAttrs, $joeWordRecord);

        // Make term for 'joe' in name
        $joeTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $joeWordRecord->getId()
        ];
        $joeTermAttrs = array_merge($joeTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $joeTermRecord = new Record(1, $joeTermAttrs);
        $this->storageShouldReceive('findBy', TermRepository::TABLE, $joeTermCriteria, null);
        $this->storageShouldReceive('create', TermRepository::TABLE, $joeTermAttrs, $joeTermRecord);

        // Make occurrence for 'joe' in name
        $joeOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $joeTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $joeOccurrenceRecord = new Record(1, $joeOccurrenceAttrs);
        $this->storageShouldReceive('create', OccurrenceRepository::TABLE, $joeOccurrenceAttrs, $joeOccurrenceRecord);

        // Make position for 'joe' in name
        $joePositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $joeOccurrenceRecord->getId(),
            PositionRepository::POSITION => $joeToken->getPosition()
        ];
        $joePositionRecord = new Record(1, $joePositionAttrs);
        $this->storageShouldReceive('create', PositionRepository::TABLE, $joePositionAttrs, $joePositionRecord);

        // Make word for 'bloggs' in name
        $bloggsWordCriteria = [WordRepository::WORD => 'bloggs'];
        $bloggsWordAttrs = $bloggsWordCriteria;
        $bloggsWordRecord = new Record(2, $bloggsWordAttrs);
        $this->storageShouldReceive('findBy', WordRepository::TABLE, $bloggsWordCriteria, null);
        $this->storageShouldReceive('create', WordRepository::TABLE, $bloggsWordAttrs, $bloggsWordRecord);

        // Make term for 'bloggs' in name
        $bloggsTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $bloggsWordRecord->getId()
        ];
        $bloggsTermAttrs = array_merge($bloggsTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $bloggsTermRecord = new Record(2, $bloggsTermAttrs);
        $this->storageShouldReceive('findBy', TermRepository::TABLE, $bloggsTermCriteria, null);
        $this->storageShouldReceive('create', TermRepository::TABLE, $bloggsTermAttrs, $bloggsTermRecord);

        // Make occurrence for 'bloggs' in name
        $bloggsOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $bloggsTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $bloggsOccurrenceRecord = new Record(2, $bloggsOccurrenceAttrs);
        $this->storageShouldReceive('create', OccurrenceRepository::TABLE, $bloggsOccurrenceAttrs, $bloggsOccurrenceRecord);

        // Make position for 'bloggs' in name
        $bloggsPositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $bloggsOccurrenceRecord->getId(),
            PositionRepository::POSITION => $bloggsToken->getPosition()
        ];
        $bloggsPositionRecord = new Record(2, $bloggsPositionAttrs);
        $this->storageShouldReceive('create', PositionRepository::TABLE, $bloggsPositionAttrs, $bloggsPositionRecord);

        // Make field for age (Value IS stored)
        // Age is not indexed so we don't bother going any further
        $ageFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->ageColumn->getId(),
            FieldRepository::VALUE => 23
        ];
        $ageFieldRecord = new Record(1, $ageFieldAttrs);
        $this->storageShouldReceive('create', FieldRepository::TABLE, $ageFieldAttrs, $ageFieldRecord);


        $document = new Indexable(123);
        $document->setField('name', 'Joe Bloggs');
        $document->setField('age', 23);

        $this->assertTrue($this->index->add($document));
    }

    /**
     * @test
     */
    public function testDocumentCanBeIndexedWhenWordsAlreadyExist()
    {
        $this->markTestSkipped('TODO');
    }

    public function testDocumentCanBeIndexedWhenTermsAlreadyExistWithinSchema()
    {
        $this->markTestSkipped('TODO');
    }

    public function testExtraFieldsAreIgnoredWhenIndexingDocument()
    {
        $this->markTestSkipped('TODO');
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