<?php

namespace BlixtTests\Index;

use Blixt\Blixt;
use Blixt\Document\Indexable;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Index\Blueprint\Blueprint;
use Blixt\Index\Blueprint\Definition;
use Blixt\Persistence\Drivers\Driver as StorageDriver;
use Blixt\Persistence\Drivers\MemoryDriver;
use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\ColumnRepository as ColRepo;
use Blixt\Persistence\Repositories\DocumentRepository as DocRepo;
use Blixt\Persistence\Repositories\FieldRepository as FldRepo;
use Blixt\Persistence\Repositories\OccurrenceRepository as OccRepo;
use Blixt\Persistence\Repositories\PositionRepository as PosRepo;
use Blixt\Persistence\Repositories\SchemaRepository as SmaRepo;
use Blixt\Persistence\Repositories\TermRepository as TrmRepo;
use Blixt\Persistence\Repositories\WordRepository as WrdRepo;
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
            ->withArgs([SmaRepo::TABLE, [SmaRepo::NAME => $schema->getName()]])
            ->andReturn(new Record($schema->getId(), [SmaRepo::NAME => $schema->getName()]));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([ColRepo::TABLE, [ColRepo::SCHEMA_ID => $schema->getId()], 0, null])
            ->andReturn(
                $schema->getColumns()->map(function (Column $column) {
                    return new Record($column->getId(), [
                        ColRepo::SCHEMA_ID => $column->getSchemaId(),
                        ColRepo::NAME => $column->getName(),
                        ColRepo::IS_INDEXED => $column->isIndexed(),
                        ColRepo::IS_STORED => $column->isStored()
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
            ->withArgs([DocRepo::TABLE, [
                DocRepo::SCHEMA_ID => $this->schema->getId(),
                DocRepo::KEY => $indexable->getKey()
            ]])
            ->andReturn(new Record($indexable->getKey(), [
                DocRepo::SCHEMA_ID => $this->schema->getId(),
                DocRepo::KEY => $indexable->getKey()
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
            ->withArgs([DocRepo::TABLE, [
                DocRepo::SCHEMA_ID => $this->schema->getId(),
                DocRepo::KEY => $indexable->getKey()
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
        $documentCriteria = [DocRepo::SCHEMA_ID => $this->schema->getId(), DocRepo::KEY => 123];
        $documentAttrs = $documentCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([DocRepo::TABLE, $documentCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([DocRepo::TABLE, $documentAttrs])
            ->andReturn($documentRecord = new Record(1, $documentAttrs));

        // Make field for name (Value is NOT stored)
        $nameFieldAttrs = [
            FldRepo::DOCUMENT_ID => $documentRecord->getId(),
            FldRepo::COLUMN_ID => $this->nameColumn->getId(),
            FldRepo::VALUE => null
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FldRepo::TABLE, $nameFieldAttrs])
            ->andReturn($nameFieldRecord = new Record(1, $nameFieldAttrs));

        // Tokenize name field
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['Joe Bloggs'])
            ->andReturn($tokens = Collection::make([
                $joeToken = new Token('joe', 0),
                $bloggsToken = new Token('bloggs', 1)
            ]));

        // Make word for 'joe' in name
        $joeWordCriteria = [WrdRepo::WORD => 'joe'];
        $joeWordAttrs = $joeWordCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([WrdRepo::TABLE, $joeWordCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([WrdRepo::TABLE, $joeWordAttrs])
            ->andReturn($joeWordRecord = new Record(1, $joeWordAttrs));

        // Make term for 'joe' in name
        $joeTermCriteria = [
            TrmRepo::SCHEMA_ID => $this->schema->getId(),
            TrmRepo::WORD_ID => $joeWordRecord->getId()
        ];
        $joeTermAttrs = array_merge($joeTermCriteria, [TrmRepo::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([TrmRepo::TABLE, $joeTermCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TrmRepo::TABLE, $joeTermAttrs])
            ->andReturn($joeTermRecord = new Record(1, $joeTermAttrs));

        // Make occurrence for 'joe' in name
        $joeOccurrenceAttrs = [
            OccRepo::FIELD_ID => $nameFieldRecord->getId(),
            OccRepo::TERM_ID => $joeTermRecord->getId(),
            OccRepo::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccRepo::TABLE, $joeOccurrenceAttrs])
            ->andReturn($joeOccurrenceRecord = new Record(1, $joeOccurrenceAttrs));

        // Make position for 'joe' in name
        $joePositionAttrs = [
            PosRepo::OCCURRENCE_ID => $joeOccurrenceRecord->getId(),
            PosRepo::POSITION => $joeToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PosRepo::TABLE, $joePositionAttrs])
            ->andReturn($joePositionRecord = new Record(1, $joePositionAttrs));

        // Make word for 'bloggs' in name
        $bloggsWordCriteria = [WrdRepo::WORD => 'bloggs'];
        $bloggsWordAttrs = $bloggsWordCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([WrdRepo::TABLE, $bloggsWordCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([WrdRepo::TABLE, $bloggsWordAttrs])
            ->andReturn($bloggsWordRecord = new Record(2, $bloggsWordAttrs));

        // Make term for 'bloggs' in name
        $bloggsTermCriteria = [
            TrmRepo::SCHEMA_ID => $this->schema->getId(),
            TrmRepo::WORD_ID => $bloggsWordRecord->getId()
        ];
        $bloggsTermAttrs = array_merge($bloggsTermCriteria, [TrmRepo::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([TrmRepo::TABLE, $bloggsTermCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TrmRepo::TABLE, $bloggsTermAttrs])
            ->andReturn($bloggsTermRecord = new Record(2, $bloggsTermAttrs));

        // Make occurrence for 'bloggs' in name
        $bloggsOccurrenceAttrs = [
            OccRepo::FIELD_ID => $nameFieldRecord->getId(),
            OccRepo::TERM_ID => $bloggsTermRecord->getId(),
            OccRepo::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccRepo::TABLE, $bloggsOccurrenceAttrs])
            ->andReturn($bloggsOccurrenceRecord = new Record(2, $bloggsOccurrenceAttrs));

        // Make position for 'bloggs' in name
        $bloggsPositionAttrs = [
            PosRepo::OCCURRENCE_ID => $bloggsOccurrenceRecord->getId(),
            PosRepo::POSITION => $bloggsToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PosRepo::TABLE, $bloggsPositionAttrs])
            ->andReturn($bloggsPositionRecord = new Record(2, $bloggsPositionAttrs));

        // Make field for age (Value IS stored)
        // Age is not indexed so we don't bother going any further
        $ageFieldAttrs = [
            FldRepo::DOCUMENT_ID => $documentRecord->getId(),
            FldRepo::COLUMN_ID => $this->ageColumn->getId(),
            FldRepo::VALUE => 23
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FldRepo::TABLE, $ageFieldAttrs])
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
        $documentCriteria = [DocRepo::SCHEMA_ID => $this->schema->getId(), DocRepo::KEY => 123];
        $documentAttrs = $documentCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([DocRepo::TABLE, $documentCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([DocRepo::TABLE, $documentAttrs])
            ->andReturn($documentRecord = new Record(1, $documentAttrs));

        // Make field for name (Value is NOT stored)
        $nameFieldAttrs = [
            FldRepo::DOCUMENT_ID => $documentRecord->getId(),
            FldRepo::COLUMN_ID => $this->nameColumn->getId(),
            FldRepo::VALUE => null
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FldRepo::TABLE, $nameFieldAttrs])
            ->andReturn($nameFieldRecord = new Record(1, $nameFieldAttrs));

        // Tokenize name field
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['Joe Bloggs'])
            ->andReturn($tokens = Collection::make([
                $joeToken = new Token('joe', 0),
                $bloggsToken = new Token('bloggs', 1)
            ]));

        // Make word for 'joe' in name
        $joeWordCriteria = [WrdRepo::WORD => 'joe'];
        $joeWordAttrs = $joeWordCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([WrdRepo::TABLE, $joeWordCriteria])
            ->andReturn($joeWordRecord = new Record(1, $joeWordAttrs));
        $this->storage->shouldNotReceive('create')->withArgs([WrdRepo::TABLE, $joeWordAttrs]);

        // Make term for 'joe' in name
        $joeTermCriteria = [
            TrmRepo::SCHEMA_ID => $this->schema->getId(),
            TrmRepo::WORD_ID => $joeWordRecord->getId()
        ];
        $joeTermAttrs = array_merge($joeTermCriteria, [TrmRepo::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([TrmRepo::TABLE, $joeTermCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TrmRepo::TABLE, $joeTermAttrs])
            ->andReturn($joeTermRecord = new Record(1, $joeTermAttrs));

        // Make occurrence for 'joe' in name
        $joeOccurrenceAttrs = [
            OccRepo::FIELD_ID => $nameFieldRecord->getId(),
            OccRepo::TERM_ID => $joeTermRecord->getId(),
            OccRepo::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccRepo::TABLE, $joeOccurrenceAttrs])
            ->andReturn($joeOccurrenceRecord = new Record(1, $joeOccurrenceAttrs));

        // Make position for 'joe' in name
        $joePositionAttrs = [
            PosRepo::OCCURRENCE_ID => $joeOccurrenceRecord->getId(),
            PosRepo::POSITION => $joeToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PosRepo::TABLE, $joePositionAttrs])
            ->andReturn($joePositionRecord = new Record(1, $joePositionAttrs));

        // Make word for 'bloggs' in name
        $bloggsWordCriteria = [WrdRepo::WORD => 'bloggs'];
        $bloggsWordAttrs = $bloggsWordCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([WrdRepo::TABLE, $bloggsWordCriteria])
            ->andReturn($bloggsWordRecord = new Record(2, $bloggsWordAttrs));
        $this->storage->shouldNotReceive('create')->withArgs([WrdRepo::TABLE, $bloggsWordAttrs]);

        // Make term for 'bloggs' in name
        $bloggsTermCriteria = [
            TrmRepo::SCHEMA_ID => $this->schema->getId(),
            TrmRepo::WORD_ID => $bloggsWordRecord->getId()
        ];
        $bloggsTermAttrs = array_merge($bloggsTermCriteria, [TrmRepo::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([TrmRepo::TABLE, $bloggsTermCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TrmRepo::TABLE, $bloggsTermAttrs])
            ->andReturn($bloggsTermRecord = new Record(2, $bloggsTermAttrs));

        // Make occurrence for 'bloggs' in name
        $bloggsOccurrenceAttrs = [
            OccRepo::FIELD_ID => $nameFieldRecord->getId(),
            OccRepo::TERM_ID => $bloggsTermRecord->getId(),
            OccRepo::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccRepo::TABLE, $bloggsOccurrenceAttrs])
            ->andReturn($bloggsOccurrenceRecord = new Record(2, $bloggsOccurrenceAttrs));

        // Make position for 'bloggs' in name
        $bloggsPositionAttrs = [
            PosRepo::OCCURRENCE_ID => $bloggsOccurrenceRecord->getId(),
            PosRepo::POSITION => $bloggsToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PosRepo::TABLE, $bloggsPositionAttrs])
            ->andReturn($bloggsPositionRecord = new Record(2, $bloggsPositionAttrs));

        // Make field for age (Value IS stored)
        // Age is not indexed so we don't bother going any further
        $ageFieldAttrs = [
            FldRepo::DOCUMENT_ID => $documentRecord->getId(),
            FldRepo::COLUMN_ID => $this->ageColumn->getId(),
            FldRepo::VALUE => 23
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FldRepo::TABLE, $ageFieldAttrs])
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
        $documentCriteria = [DocRepo::SCHEMA_ID => $this->schema->getId(), DocRepo::KEY => 123];
        $documentAttrs = $documentCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([DocRepo::TABLE, $documentCriteria])
            ->andReturnNull();
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([DocRepo::TABLE, $documentAttrs])
            ->andReturn($documentRecord = new Record(1, $documentAttrs));

        // Make field for name (Value is NOT stored)
        $nameFieldAttrs = [
            FldRepo::DOCUMENT_ID => $documentRecord->getId(),
            FldRepo::COLUMN_ID => $this->nameColumn->getId(),
            FldRepo::VALUE => null
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FldRepo::TABLE, $nameFieldAttrs])
            ->andReturn($nameFieldRecord = new Record(1, $nameFieldAttrs));

        // Tokenize name field
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['Joe Bloggs'])
            ->andReturn($tokens = Collection::make([
                $joeToken = new Token('joe', 0),
                $bloggsToken = new Token('bloggs', 1)
            ]));

        // Make word for 'joe' in name
        $joeWordCriteria = [WrdRepo::WORD => 'joe'];
        $joeWordAttrs = $joeWordCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([WrdRepo::TABLE, $joeWordCriteria])
            ->andReturn($joeWordRecord = new Record(1, $joeWordAttrs));
        $this->storage->shouldNotReceive('create')->withArgs([WrdRepo::TABLE, $joeWordAttrs]);

        // Make term for 'joe' in name
        $joeTermCriteria = [
            TrmRepo::SCHEMA_ID => $this->schema->getId(),
            TrmRepo::WORD_ID => $joeWordRecord->getId()
        ];
        $joeTermAttrs = array_merge($joeTermCriteria, [TrmRepo::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([TrmRepo::TABLE, $joeTermCriteria])
            ->andReturn($joeTermRecord = new Record(1, $joeTermAttrs));
        $this->storage->shouldNotReceive('create')->withArgs([TrmRepo::TABLE, $joeTermAttrs]);
        $updatedJoeTermAttrs = array_merge($joeTermAttrs, [TrmRepo::FIELD_COUNT => 2]);
        $this->storage->shouldReceive('update')
            ->once()
            ->withArgs([TrmRepo::TABLE, $joeTermRecord->getId(), $updatedJoeTermAttrs])
            ->andReturn($updatedJoeTermRecord = new Record($joeTermRecord->getId(), $updatedJoeTermAttrs));

        // Make occurrence for 'joe' in name
        $joeOccurrenceAttrs = [
            OccRepo::FIELD_ID => $nameFieldRecord->getId(),
            OccRepo::TERM_ID => $updatedJoeTermRecord->getId(),
            OccRepo::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccRepo::TABLE, $joeOccurrenceAttrs])
            ->andReturn($joeOccurrenceRecord = new Record(1, $joeOccurrenceAttrs));

        // Make position for 'joe' in name
        $joePositionAttrs = [
            PosRepo::OCCURRENCE_ID => $joeOccurrenceRecord->getId(),
            PosRepo::POSITION => $joeToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PosRepo::TABLE, $joePositionAttrs])
            ->andReturn($joePositionRecord = new Record(1, $joePositionAttrs));

        // Make word for 'bloggs' in name
        $bloggsWordCriteria = [WrdRepo::WORD => 'bloggs'];
        $bloggsWordAttrs = $bloggsWordCriteria;
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([WrdRepo::TABLE, $bloggsWordCriteria])
            ->andReturn($bloggsWordRecord = new Record(2, $bloggsWordAttrs));
        $this->storage->shouldNotReceive('create')->withArgs([WrdRepo::TABLE, $bloggsWordAttrs]);

        // Make term for 'bloggs' in name
        $bloggsTermCriteria = [
            TrmRepo::SCHEMA_ID => $this->schema->getId(),
            TrmRepo::WORD_ID => $bloggsWordRecord->getId()
        ];
        $bloggsTermAttrs = array_merge($bloggsTermCriteria, [TrmRepo::FIELD_COUNT => 1]);
        $this->storage->shouldReceive('findBy')
            ->once()
            ->withArgs([TrmRepo::TABLE, $bloggsTermCriteria])
            ->andReturn($bloggsTermRecord = new Record(2, $bloggsTermAttrs));
        $this->storage->shouldNotReceive('create')->withArgs([TrmRepo::TABLE, $bloggsTermAttrs]);
        $updatedBloggsTermAttrs = array_merge($bloggsTermAttrs, [TrmRepo::FIELD_COUNT => 2]);
        $this->storage->shouldReceive('update')
            ->once()
            ->withArgs([TrmRepo::TABLE, $bloggsTermRecord->getId(), $updatedBloggsTermAttrs])
            ->andReturn($updatedBloggsTermRecord = new Record($bloggsTermRecord->getId(), $updatedBloggsTermAttrs));

        // Make occurrence for 'bloggs' in name
        $bloggsOccurrenceAttrs = [
            OccRepo::FIELD_ID => $nameFieldRecord->getId(),
            OccRepo::TERM_ID => $updatedBloggsTermRecord->getId(),
            OccRepo::FREQUENCY => 1
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([OccRepo::TABLE, $bloggsOccurrenceAttrs])
            ->andReturn($bloggsOccurrenceRecord = new Record(2, $bloggsOccurrenceAttrs));

        // Make position for 'bloggs' in name
        $bloggsPositionAttrs = [
            PosRepo::OCCURRENCE_ID => $bloggsOccurrenceRecord->getId(),
            PosRepo::POSITION => $bloggsToken->getPosition()
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([PosRepo::TABLE, $bloggsPositionAttrs])
            ->andReturn($bloggsPositionRecord = new Record(2, $bloggsPositionAttrs));

        // Make field for age (Value IS stored)
        // Age is not indexed so we don't bother going any further
        $ageFieldAttrs = [
            FldRepo::DOCUMENT_ID => $documentRecord->getId(),
            FldRepo::COLUMN_ID => $this->ageColumn->getId(),
            FldRepo::VALUE => 23
        ];
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([FldRepo::TABLE, $ageFieldAttrs])
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
        $storage = new MemoryDriver();
        $storage->install();
        $tokenizer = new DummyTokenizer(new DummyStemmer());
        $blixt = new Blixt($storage, $tokenizer);

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
            SmaRepo::TABLE => [
                1 => [SmaRepo::NAME => 'people']
            ],
            ColRepo::TABLE => [
                1 => [
                    ColRepo::SCHEMA_ID => 1,
                    ColRepo::NAME => 'name',
                    ColRepo::IS_INDEXED => true,
                    ColRepo::IS_STORED => false
                ],
                2 => [
                    ColRepo::SCHEMA_ID => 1,
                    ColRepo::NAME => 'age',
                    ColRepo::IS_INDEXED => false,
                    ColRepo::IS_STORED => true
                ]
            ],
            WrdRepo::TABLE => [
                1 => [
                    WrdRepo::WORD => 'joe'
                ],
                2 => [
                    WrdRepo::WORD => 'bloggs'
                ]
            ],
            TrmRepo::TABLE => [
                1 => [
                    TrmRepo::SCHEMA_ID => 1,
                    TrmRepo::WORD_ID => 1,
                    TrmRepo::FIELD_COUNT => 1
                ],
                2 => [
                    TrmRepo::SCHEMA_ID => 1,
                    TrmRepo::WORD_ID => 2,
                    TrmRepo::FIELD_COUNT => 1
                ]
            ],
            DocRepo::TABLE => [
                1 => [
                    DocRepo::SCHEMA_ID => 1,
                    DocRepo::KEY => 1
                ]
            ],
            FldRepo::TABLE => [
                1 => [
                    FldRepo::DOCUMENT_ID => 1,
                    FldRepo::COLUMN_ID => 1,
                    FldRepo::VALUE => null
                ],
                2 => [
                    FldRepo::DOCUMENT_ID => 1,
                    FldRepo::COLUMN_ID => 2,
                    FldRepo::VALUE => 30
                ]
            ],
            OccRepo::TABLE => [
                1 => [
                    OccRepo::FIELD_ID => 1,
                    OccRepo::TERM_ID => 1,
                    OccRepo::FREQUENCY => 1
                ],
                2 => [
                    OccRepo::FIELD_ID => 1,
                    OccRepo::TERM_ID => 2,
                    OccRepo::FREQUENCY => 1
                ]
            ],
            PosRepo::TABLE => [
                1 => [
                    PosRepo::OCCURRENCE_ID => 1,
                    PosRepo::POSITION => 0
                ],
                2 => [
                    PosRepo::OCCURRENCE_ID => 2,
                    PosRepo::POSITION => 1
                ]
            ]
        ];

        $expectedPeopleJoeBloggsJaneDoe = [
            SmaRepo::TABLE => [
                1 => [
                    SmaRepo::NAME => 'people'
                ]
            ],
            ColRepo::TABLE => [
                1 => [
                    ColRepo::SCHEMA_ID => 1,
                    ColRepo::NAME => 'name',
                    ColRepo::IS_INDEXED => true,
                    ColRepo::IS_STORED => false
                ],
                2 => [
                    ColRepo::SCHEMA_ID => 1,
                    ColRepo::NAME => 'age',
                    ColRepo::IS_INDEXED => false,
                    ColRepo::IS_STORED => true
                ]
            ],
            WrdRepo::TABLE => [
                1 => [
                    WrdRepo::WORD => 'joe'
                ],
                2 => [
                    WrdRepo::WORD => 'bloggs'
                ],
                3 => [
                    WrdRepo::WORD => 'jane'
                ],
                4 => [
                    WrdRepo::WORD => 'doe'
                ]
            ],
            TrmRepo::TABLE => [
                1 => [TrmRepo::SCHEMA_ID => 1, TrmRepo::WORD_ID => 1, TrmRepo::FIELD_COUNT => 1],
                2 => [TrmRepo::SCHEMA_ID => 1, TrmRepo::WORD_ID => 2, TrmRepo::FIELD_COUNT => 1],
                3 => [TrmRepo::SCHEMA_ID => 1, TrmRepo::WORD_ID => 3, TrmRepo::FIELD_COUNT => 1],
                4 => [TrmRepo::SCHEMA_ID => 1, TrmRepo::WORD_ID => 4, TrmRepo::FIELD_COUNT => 1]
            ],
            DocRepo::TABLE => [
                1 => [
                    DocRepo::SCHEMA_ID => 1,
                    DocRepo::KEY => 1
                ],
                2 => [
                    DocRepo::SCHEMA_ID => 1,
                    DocRepo::KEY => 2
                ]
            ],
            FldRepo::TABLE => [
                1 => [
                    FldRepo::DOCUMENT_ID => 1,
                    FldRepo::COLUMN_ID => 1,
                    FldRepo::VALUE => null
                ],
                2 => [
                    FldRepo::DOCUMENT_ID => 1,
                    FldRepo::COLUMN_ID => 2,
                    FldRepo::VALUE => 30
                ],
                3 => [
                    FldRepo::DOCUMENT_ID => 2,
                    FldRepo::COLUMN_ID => 1,
                    FldRepo::VALUE => null
                ],
                4 => [
                    FldRepo::DOCUMENT_ID => 2,
                    FldRepo::COLUMN_ID => 2,
                    FldRepo::VALUE => 28
                ]
            ],
            OccRepo::TABLE => [
                1 => [
                    OccRepo::FIELD_ID => 1,
                    OccRepo::TERM_ID => 1,
                    OccRepo::FREQUENCY => 1
                ],
                2 => [
                    OccRepo::FIELD_ID => 1,
                    OccRepo::TERM_ID => 2,
                    OccRepo::FREQUENCY => 1
                ],
                3 => [
                    OccRepo::FIELD_ID => 3,
                    OccRepo::TERM_ID => 3,
                    OccRepo::FREQUENCY => 1
                ],
                4 => [
                    OccRepo::FIELD_ID => 3,
                    OccRepo::TERM_ID => 4,
                    OccRepo::FREQUENCY => 1
                ]
            ],
            PosRepo::TABLE => [
                1 => [
                    PosRepo::OCCURRENCE_ID => 1,
                    PosRepo::POSITION => 0
                ],
                2 => [
                    PosRepo::OCCURRENCE_ID => 2,
                    PosRepo::POSITION => 1
                ],
                3 => [
                    PosRepo::OCCURRENCE_ID => 3,
                    PosRepo::POSITION => 0
                ],
                4 => [
                    PosRepo::OCCURRENCE_ID => 4,
                    PosRepo::POSITION => 1
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