<?php

namespace BlixtTests\Index;

use Blixt\Blixt;
use Blixt\Document\Indexable;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Index\Blueprint\Blueprint;
use Blixt\Index\Blueprint\Definition;
use Blixt\Persistence\Drivers\MemoryStorage;
use Blixt\Persistence\Drivers\Storage;
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

class IndexTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Storage
     */
    protected $storage;

    /**
     * @var \Mockery\MockInterface|\Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Stemming\Stemmer|\Mockery\MockInterface
     */
    protected $stemmer;

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
        $this->storage = m::mock(Storage::class);
        $this->tokenizer = m::mock(Tokenizer::class);
        $this->stemmer = m::mock(Stemmer::class);

        $this->blixt = new Blixt($this->storage, $this->tokenizer, $this->stemmer);
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

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $schema->getName()], 0, 1])
            ->andReturn([new Record($schema->getId(), [SchemaRepository::NAME => $schema->getName()])]);

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

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([DocumentRepository::TABLE, [
                DocumentRepository::SCHEMA_ID => $this->schema->getId(),
                DocumentRepository::KEY => $indexable->getKey()
            ], 0, 1])
            ->andReturn([new Record($indexable->getKey(), [
                DocumentRepository::SCHEMA_ID => $this->schema->getId(),
                DocumentRepository::KEY => $indexable->getKey()
            ])]);

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

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([DocumentRepository::TABLE, [
                DocumentRepository::SCHEMA_ID => $this->schema->getId(),
                DocumentRepository::KEY => $indexable->getKey()
            ], 0, 1])
            ->andReturn([]);

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
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([DocumentRepository::TABLE, $documentCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([DocumentRepository::TABLE, $documentAttrs])
            ->andReturn($documentRecord = new Record(1, $documentAttrs));

        // Make field for name (Value is NOT stored)
        $nameFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->nameColumn->getId(),
            FieldRepository::VALUE => null
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FieldRepository::TABLE, $nameFieldAttrs])
            ->andReturn($nameFieldRecord = new Record(1, $nameFieldAttrs));

        // Tokenize name field
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['Joe Bloggs'])
            ->andReturn($tokens = Collection::make([
                $joeToken = new Token('joe', 0),
                $bloggsToken = new Token('bloggs', 1)
            ]));

        // Stem the text of each returned token
        $this->stemmer->shouldReceive('stem')
            ->once()
            ->withArgs(['joe'])
            ->andReturn('joe');
        $this->stemmer->shouldReceive('stem')
            ->once()
            ->withArgs(['bloggs'])
            ->andReturn('bloggs');

        // Make word for 'joe' in name
        $joeWordCriteria = [WordRepository::WORD => 'joe'];
        $joeWordAttrs = $joeWordCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, $joeWordCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([WordRepository::TABLE, $joeWordAttrs])
            ->andReturn($joeWordRecord = new Record(1, $joeWordAttrs));

        // Make term for 'joe' in name
        $joeTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $joeWordRecord->getId()
        ];
        $joeTermAttrs = array_merge($joeTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, $joeTermCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TermRepository::TABLE, $joeTermAttrs])
            ->andReturn($joeTermRecord = new Record(1, $joeTermAttrs));

        // Make occurrence for 'joe' in name
        $joeOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $joeTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccurrenceRepository::TABLE, $joeOccurrenceAttrs])
            ->andReturn($joeOccurrenceRecord = new Record(1, $joeOccurrenceAttrs));

        // Make position for 'joe' in name
        $joePositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $joeOccurrenceRecord->getId(),
            PositionRepository::POSITION => $joeToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PositionRepository::TABLE, $joePositionAttrs])
            ->andReturn($joePositionRecord = new Record(1, $joePositionAttrs));

        // Make word for 'bloggs' in name
        $bloggsWordCriteria = [WordRepository::WORD => 'bloggs'];
        $bloggsWordAttrs = $bloggsWordCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, $bloggsWordCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([WordRepository::TABLE, $bloggsWordAttrs])
            ->andReturn($bloggsWordRecord = new Record(2, $bloggsWordAttrs));

        // Make term for 'bloggs' in name
        $bloggsTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $bloggsWordRecord->getId()
        ];
        $bloggsTermAttrs = array_merge($bloggsTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, $bloggsTermCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TermRepository::TABLE, $bloggsTermAttrs])
            ->andReturn($bloggsTermRecord = new Record(2, $bloggsTermAttrs));

        // Make occurrence for 'bloggs' in name
        $bloggsOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $bloggsTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccurrenceRepository::TABLE, $bloggsOccurrenceAttrs])
            ->andReturn($bloggsOccurrenceRecord = new Record(2, $bloggsOccurrenceAttrs));

        // Make position for 'bloggs' in name
        $bloggsPositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $bloggsOccurrenceRecord->getId(),
            PositionRepository::POSITION => $bloggsToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PositionRepository::TABLE, $bloggsPositionAttrs])
            ->andReturn($bloggsPositionRecord = new Record(2, $bloggsPositionAttrs));

        // Make field for age (Value IS stored)
        // Age is not indexed so we don't bother going any further
        $ageFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->ageColumn->getId(),
            FieldRepository::VALUE => 23
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FieldRepository::TABLE, $ageFieldAttrs])
            ->andReturn($ageFieldRecord = new Record(1, $ageFieldAttrs));

        $document = new Indexable(123);
        $document->setField('name', 'Joe Bloggs');
        $document->setField('age', 23);

        $this->assertTrue($this->index->add($document));
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
    public function testDocumentCanBeIndexedWhenWordsAlreadyExist()
    {
        $this->makeIndexForPeopleSchemaWithNameAndAgeColumns();

        // Find document by its schema ID and key, returns null (doesn't exist). Create new document.
        $documentCriteria = [DocumentRepository::SCHEMA_ID => $this->schema->getId(), DocumentRepository::KEY => 123];
        $documentAttrs = $documentCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([DocumentRepository::TABLE, $documentCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([DocumentRepository::TABLE, $documentAttrs])
            ->andReturn($documentRecord = new Record(1, $documentAttrs));

        // Make field for name (Value is NOT stored)
        $nameFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->nameColumn->getId(),
            FieldRepository::VALUE => null
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FieldRepository::TABLE, $nameFieldAttrs])
            ->andReturn($nameFieldRecord = new Record(1, $nameFieldAttrs));

        // Tokenize name field
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['Joe Bloggs'])
            ->andReturn($tokens = Collection::make([
                $joeToken = new Token('joe', 0),
                $bloggsToken = new Token('bloggs', 1)
            ]));

        // Stem the text of each returned token
        $this->stemmer->shouldReceive('stem')
            ->once()
            ->withArgs(['joe'])
            ->andReturn('joe');
        $this->stemmer->shouldReceive('stem')
            ->once()
            ->withArgs(['bloggs'])
            ->andReturn('bloggs');

        // Make word for 'joe' in name
        $joeWordCriteria = [WordRepository::WORD => 'joe'];
        $joeWordAttrs = $joeWordCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, $joeWordCriteria, 0, 1])
            ->andReturn([$joeWordRecord = new Record(1, $joeWordAttrs)]);
        $this->storage->shouldNotReceive('create')->withArgs([WordRepository::TABLE, $joeWordAttrs]);

        // Make term for 'joe' in name
        $joeTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $joeWordRecord->getId()
        ];
        $joeTermAttrs = array_merge($joeTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, $joeTermCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TermRepository::TABLE, $joeTermAttrs])
            ->andReturn($joeTermRecord = new Record(1, $joeTermAttrs));

        // Make occurrence for 'joe' in name
        $joeOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $joeTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccurrenceRepository::TABLE, $joeOccurrenceAttrs])
            ->andReturn($joeOccurrenceRecord = new Record(1, $joeOccurrenceAttrs));

        // Make position for 'joe' in name
        $joePositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $joeOccurrenceRecord->getId(),
            PositionRepository::POSITION => $joeToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PositionRepository::TABLE, $joePositionAttrs])
            ->andReturn($joePositionRecord = new Record(1, $joePositionAttrs));

        // Make word for 'bloggs' in name
        $bloggsWordCriteria = [WordRepository::WORD => 'bloggs'];
        $bloggsWordAttrs = $bloggsWordCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, $bloggsWordCriteria, 0, 1])
            ->andReturn([$bloggsWordRecord = new Record(2, $bloggsWordAttrs)]);
        $this->storage->shouldNotReceive('create')->withArgs([WordRepository::TABLE, $bloggsWordAttrs]);

        // Make term for 'bloggs' in name
        $bloggsTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $bloggsWordRecord->getId()
        ];
        $bloggsTermAttrs = array_merge($bloggsTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, $bloggsTermCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TermRepository::TABLE, $bloggsTermAttrs])
            ->andReturn($bloggsTermRecord = new Record(2, $bloggsTermAttrs));

        // Make occurrence for 'bloggs' in name
        $bloggsOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $bloggsTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccurrenceRepository::TABLE, $bloggsOccurrenceAttrs])
            ->andReturn($bloggsOccurrenceRecord = new Record(2, $bloggsOccurrenceAttrs));

        // Make position for 'bloggs' in name
        $bloggsPositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $bloggsOccurrenceRecord->getId(),
            PositionRepository::POSITION => $bloggsToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PositionRepository::TABLE, $bloggsPositionAttrs])
            ->andReturn($bloggsPositionRecord = new Record(2, $bloggsPositionAttrs));

        // Make field for age (Value IS stored)
        // Age is not indexed so we don't bother going any further
        $ageFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->ageColumn->getId(),
            FieldRepository::VALUE => 23
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FieldRepository::TABLE, $ageFieldAttrs])
            ->andReturn($ageFieldRecord = new Record(1, $ageFieldAttrs));

        $document = new Indexable(123);
        $document->setField('name', 'Joe Bloggs');
        $document->setField('age', 23);

        $this->assertTrue($this->index->add($document));
    }

    /**
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testDocumentCanBeIndexedWhenTermsAlreadyExistWithinSchema()
    {
        $this->makeIndexForPeopleSchemaWithNameAndAgeColumns();

        // Find document by its schema ID and key, returns null (doesn't exist). Create new document.
        $documentCriteria = [DocumentRepository::SCHEMA_ID => $this->schema->getId(), DocumentRepository::KEY => 123];
        $documentAttrs = $documentCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([DocumentRepository::TABLE, $documentCriteria, 0, 1])
            ->andReturn([]);
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([DocumentRepository::TABLE, $documentAttrs])
            ->andReturn($documentRecord = new Record(1, $documentAttrs));

        // Make field for name (Value is NOT stored)
        $nameFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->nameColumn->getId(),
            FieldRepository::VALUE => null
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FieldRepository::TABLE, $nameFieldAttrs])
            ->andReturn($nameFieldRecord = new Record(1, $nameFieldAttrs));

        // Tokenize name field
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['Joe Bloggs'])
            ->andReturn($tokens = Collection::make([
                $joeToken = new Token('joe', 0),
                $bloggsToken = new Token('bloggs', 1)
            ]));

        // Stem the text of each returned token
        $this->stemmer->shouldReceive('stem')
            ->once()
            ->withArgs(['joe'])
            ->andReturn('joe');
        $this->stemmer->shouldReceive('stem')
            ->once()
            ->withArgs(['bloggs'])
            ->andReturn('bloggs');

        // Make word for 'joe' in name
        $joeWordCriteria = [WordRepository::WORD => 'joe'];
        $joeWordAttrs = $joeWordCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, $joeWordCriteria, 0, 1])
            ->andReturn([$joeWordRecord = new Record(1, $joeWordAttrs)]);
        $this->storage->shouldNotReceive('create')->withArgs([WordRepository::TABLE, $joeWordAttrs]);

        // Make term for 'joe' in name
        $joeTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $joeWordRecord->getId()
        ];
        $joeTermAttrs = array_merge($joeTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, $joeTermCriteria, 0, 1])
            ->andReturn([$joeTermRecord = new Record(1, $joeTermAttrs)]);
        $this->storage->shouldNotReceive('create')->withArgs([TermRepository::TABLE, $joeTermAttrs]);
        $updatedJoeTermAttrs = array_merge($joeTermAttrs, [TermRepository::FIELD_COUNT => 2]);
        $this->storage->shouldReceive('update')
            ->once()
            ->withArgs([TermRepository::TABLE, $joeTermRecord->getId(), $updatedJoeTermAttrs])
            ->andReturn($updatedJoeTermRecord = new Record($joeTermRecord->getId(), $updatedJoeTermAttrs));

        // Make occurrence for 'joe' in name
        $joeOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $updatedJoeTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccurrenceRepository::TABLE, $joeOccurrenceAttrs])
            ->andReturn($joeOccurrenceRecord = new Record(1, $joeOccurrenceAttrs));

        // Make position for 'joe' in name
        $joePositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $joeOccurrenceRecord->getId(),
            PositionRepository::POSITION => $joeToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PositionRepository::TABLE, $joePositionAttrs])
            ->andReturn($joePositionRecord = new Record(1, $joePositionAttrs));

        // Make word for 'bloggs' in name
        $bloggsWordCriteria = [WordRepository::WORD => 'bloggs'];
        $bloggsWordAttrs = $bloggsWordCriteria;
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, $bloggsWordCriteria, 0, 1])
            ->andReturn([$bloggsWordRecord = new Record(2, $bloggsWordAttrs)]);
        $this->storage->shouldNotReceive('create')->withArgs([WordRepository::TABLE, $bloggsWordAttrs]);

        // Make term for 'bloggs' in name
        $bloggsTermCriteria = [
            TermRepository::SCHEMA_ID => $this->schema->getId(),
            TermRepository::WORD_ID => $bloggsWordRecord->getId()
        ];
        $bloggsTermAttrs = array_merge($bloggsTermCriteria, [TermRepository::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, $bloggsTermCriteria, 0, 1])
            ->andReturn([$bloggsTermRecord = new Record(2, $bloggsTermAttrs)]);
        $this->storage->shouldNotReceive('create')->withArgs([TermRepository::TABLE, $bloggsTermAttrs]);
        $updatedBloggsTermAttrs = array_merge($bloggsTermAttrs, [TermRepository::FIELD_COUNT => 2]);
        $this->storage->shouldReceive('update')
            ->once()
            ->withArgs([TermRepository::TABLE, $bloggsTermRecord->getId(), $updatedBloggsTermAttrs])
            ->andReturn($updatedBloggsTermRecord = new Record($bloggsTermRecord->getId(), $updatedBloggsTermAttrs));

        // Make occurrence for 'bloggs' in name
        $bloggsOccurrenceAttrs = [
            OccurrenceRepository::FIELD_ID => $nameFieldRecord->getId(),
            OccurrenceRepository::TERM_ID => $updatedBloggsTermRecord->getId(),
            OccurrenceRepository::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccurrenceRepository::TABLE, $bloggsOccurrenceAttrs])
            ->andReturn($bloggsOccurrenceRecord = new Record(2, $bloggsOccurrenceAttrs));

        // Make position for 'bloggs' in name
        $bloggsPositionAttrs = [
            PositionRepository::OCCURRENCE_ID => $bloggsOccurrenceRecord->getId(),
            PositionRepository::POSITION => $bloggsToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PositionRepository::TABLE, $bloggsPositionAttrs])
            ->andReturn($bloggsPositionRecord = new Record(2, $bloggsPositionAttrs));

        // Make field for age (Value IS stored)
        // Age is not indexed so we don't bother going any further
        $ageFieldAttrs = [
            FieldRepository::DOCUMENT_ID => $documentRecord->getId(),
            FieldRepository::COLUMN_ID => $this->ageColumn->getId(),
            FieldRepository::VALUE => 23
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FieldRepository::TABLE, $ageFieldAttrs])
            ->andReturn($ageFieldRecord = new Record(1, $ageFieldAttrs));

        $document = new Indexable(123);
        $document->setField('name', 'Joe Bloggs');
        $document->setField('age', 23);

        $this->assertTrue($this->index->add($document));
    }

    /**
     * @dataProvider documentsIndexedCorrectlyProvider
     * @test
     *
     * @param $blueprint
     * @param $indexables
     * @param $expected
     *
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testDocumentsAreCorrectlyIndexed($blueprint, $indexables, $expected)
    {
        $storage = new MemoryStorage();
        $storage->install();
        $stemmer = new DummyStemmer();
        $tokenizer = new DummyTokenizer();
        $blixt = new Blixt($storage, $tokenizer, $stemmer);

        $index = $blixt->create($blueprint);
        foreach (is_array($indexables) ? $indexables : [$indexables] as $indexable) {
            $index->add($indexable);
        }

        $this->assertEquals($expected, $this->getInaccessibleProperty($storage, 'data'));
    }

    public function documentsIndexedCorrectlyProvider()
    {
        $peopleBlueprint = new Blueprint('people', Collection::make([
            new Definition('name', true, false),
            new Definition('age', false, true)
        ]));

        $joeBloggsIndexable = new Indexable(1, Collection::make([
            'name' => 'Joe Bloggs',
            'age' => 30
        ]));

        $janeDoeIndexable = new Indexable(2, Collection::make([
            'name' => 'Jane Doe',
            'age' => 28
        ]));

        $expectedPeopleJoeBloggs = [
            SchemaRepository::TABLE => [
                1 => [SchemaRepository::NAME => 'people']
            ],
            ColumnRepository::TABLE => [
                1 => [
                    ColumnRepository::SCHEMA_ID => 1,
                    ColumnRepository::NAME => 'name',
                    ColumnRepository::IS_INDEXED => true,
                    ColumnRepository::IS_STORED => false
                ],
                2 => [
                    ColumnRepository::SCHEMA_ID => 1,
                    ColumnRepository::NAME => 'age',
                    ColumnRepository::IS_INDEXED => false,
                    ColumnRepository::IS_STORED => true
                ]
            ],
            WordRepository::TABLE => [
                1 => [
                    WordRepository::WORD => 'joe'
                ],
                2 => [
                    WordRepository::WORD => 'bloggs'
                ]
            ],
            TermRepository::TABLE => [
                1 => [
                    TermRepository::SCHEMA_ID => 1,
                    TermRepository::WORD_ID => 1,
                    TermRepository::FIELD_COUNT => 1
                ],
                2 => [
                    TermRepository::SCHEMA_ID => 1,
                    TermRepository::WORD_ID => 2,
                    TermRepository::FIELD_COUNT => 1
                ]
            ],
            DocumentRepository::TABLE => [
                1 => [
                    DocumentRepository::SCHEMA_ID => 1,
                    DocumentRepository::KEY => 1
                ]
            ],
            FieldRepository::TABLE => [
                1 => [
                    FieldRepository::DOCUMENT_ID => 1,
                    FieldRepository::COLUMN_ID => 1,
                    FieldRepository::VALUE => null
                ],
                2 => [
                    FieldRepository::DOCUMENT_ID => 1,
                    FieldRepository::COLUMN_ID => 2,
                    FieldRepository::VALUE => 30
                ]
            ],
            OccurrenceRepository::TABLE => [
                1 => [
                    OccurrenceRepository::FIELD_ID => 1,
                    OccurrenceRepository::TERM_ID => 1,
                    OccurrenceRepository::FREQUENCY => 1
                ],
                2 => [
                    OccurrenceRepository::FIELD_ID => 1,
                    OccurrenceRepository::TERM_ID => 2,
                    OccurrenceRepository::FREQUENCY => 1
                ]
            ],
            PositionRepository::TABLE => [
                1 => [
                    PositionRepository::OCCURRENCE_ID => 1,
                    PositionRepository::POSITION => 0
                ],
                2 => [
                    PositionRepository::OCCURRENCE_ID => 2,
                    PositionRepository::POSITION => 1
                ]
            ]
        ];

        $expectedPeopleJoeBloggsJaneDoe = [
            SchemaRepository::TABLE => [
                1 => [
                    SchemaRepository::NAME => 'people'
                ]
            ],
            ColumnRepository::TABLE => [
                1 => [
                    ColumnRepository::SCHEMA_ID => 1,
                    ColumnRepository::NAME => 'name',
                    ColumnRepository::IS_INDEXED => true,
                    ColumnRepository::IS_STORED => false
                ],
                2 => [
                    ColumnRepository::SCHEMA_ID => 1,
                    ColumnRepository::NAME => 'age',
                    ColumnRepository::IS_INDEXED => false,
                    ColumnRepository::IS_STORED => true
                ]
            ],
            WordRepository::TABLE => [
                1 => [
                    WordRepository::WORD => 'joe'
                ],
                2 => [
                    WordRepository::WORD => 'bloggs'
                ],
                3 => [
                    WordRepository::WORD => 'jane'
                ],
                4 => [
                    WordRepository::WORD => 'doe'
                ]
            ],
            TermRepository::TABLE => [
                1 => [
                    TermRepository::SCHEMA_ID => 1,
                    TermRepository::WORD_ID => 1,
                    TermRepository::FIELD_COUNT => 1
                ],
                2 => [
                    TermRepository::SCHEMA_ID => 1,
                    TermRepository::WORD_ID => 2,
                    TermRepository::FIELD_COUNT => 1
                ],
                3 => [
                    TermRepository::SCHEMA_ID => 1,
                    TermRepository::WORD_ID => 3,
                    TermRepository::FIELD_COUNT => 1
                ],
                4 => [
                    TermRepository::SCHEMA_ID => 1,
                    TermRepository::WORD_ID => 4,
                    TermRepository::FIELD_COUNT => 1
                ]
            ],
            DocumentRepository::TABLE => [
                1 => [
                    DocumentRepository::SCHEMA_ID => 1,
                    DocumentRepository::KEY => 1
                ],
                2 => [
                    DocumentRepository::SCHEMA_ID => 1,
                    DocumentRepository::KEY => 2
                ]
            ],
            FieldRepository::TABLE => [
                1 => [
                    FieldRepository::DOCUMENT_ID => 1,
                    FieldRepository::COLUMN_ID => 1,
                    FieldRepository::VALUE => null
                ],
                2 => [
                    FieldRepository::DOCUMENT_ID => 1,
                    FieldRepository::COLUMN_ID => 2,
                    FieldRepository::VALUE => 30
                ],
                3 => [
                    FieldRepository::DOCUMENT_ID => 2,
                    FieldRepository::COLUMN_ID => 1,
                    FieldRepository::VALUE => null
                ],
                4 => [
                    FieldRepository::DOCUMENT_ID => 2,
                    FieldRepository::COLUMN_ID => 2,
                    FieldRepository::VALUE => 28
                ]
            ],
            OccurrenceRepository::TABLE => [
                1 => [
                    OccurrenceRepository::FIELD_ID => 1,
                    OccurrenceRepository::TERM_ID => 1,
                    OccurrenceRepository::FREQUENCY => 1
                ],
                2 => [
                    OccurrenceRepository::FIELD_ID => 1,
                    OccurrenceRepository::TERM_ID => 2,
                    OccurrenceRepository::FREQUENCY => 1
                ],
                3 => [
                    OccurrenceRepository::FIELD_ID => 3,
                    OccurrenceRepository::TERM_ID => 3,
                    OccurrenceRepository::FREQUENCY => 1
                ],
                4 => [
                    OccurrenceRepository::FIELD_ID => 3,
                    OccurrenceRepository::TERM_ID => 4,
                    OccurrenceRepository::FREQUENCY => 1
                ]
            ],
            PositionRepository::TABLE => [
                1 => [
                    PositionRepository::OCCURRENCE_ID => 1,
                    PositionRepository::POSITION => 0
                ],
                2 => [
                    PositionRepository::OCCURRENCE_ID => 2,
                    PositionRepository::POSITION => 1
                ],
                3 => [
                    PositionRepository::OCCURRENCE_ID => 3,
                    PositionRepository::POSITION => 0
                ],
                4 => [
                    PositionRepository::OCCURRENCE_ID => 4,
                    PositionRepository::POSITION => 1
                ]
            ]
        ];

        return [
            'people schema, joe bloggs' => [
                $peopleBlueprint, [$joeBloggsIndexable], $expectedPeopleJoeBloggs
            ],
            'people schema, joe bloggs and jane doe' => [
                $peopleBlueprint, [$joeBloggsIndexable, $janeDoeIndexable], $expectedPeopleJoeBloggsJaneDoe
            ],
        ];
    }
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
    public function tokenize(string $text, array $prefixes = []): Collection
    {
        $tokens = new Collection();

        $i = 0;
        foreach (explode(' ', mb_strtolower(trim($text))) as $word) {
            $tokens->push(new Token($word, $i++));
        }

        return $tokens;
    }
}
