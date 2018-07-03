<?php

namespace BlixtTests;

use Blixt\Blixt;
use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Exceptions\SchemaDoesNotExistException;
use Blixt\Exceptions\InvalidBlueprintException;
use Blixt\Index\Blueprint\Definition;
use Blixt\Index\Index;
use Blixt\Index\Blueprint\Blueprint;
use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Drivers\Driver as StorageDriver;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\ColumnRepository;
use Blixt\Persistence\Repositories\SchemaRepository;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;
use Mockery as m;

class BlixtTest extends TestCase
{
    /**
     * @var \Blixt\Blixt
     */
    protected $blixt;

    /**
     * @var \Mockery\MockInterface
     */
    protected $storage;

    /**
     * @var \Mockery\MockInterface
     */
    protected $tokenizer;

    public function setUp()
    {
        $this->blixt = new Blixt(
            $this->storage = m::mock(StorageDriver::class),
            $this->tokenizer = m::mock(Tokenizer::class)
        );
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(Blixt::class, $this->blixt);
    }

    /**
     * @test
     */
    public function testGetStorageReturnsStorageContainingSameDriverPassedIntoConstructor()
    {
        $this->assertSame($this->storage, $this->blixt->getStorage()->getDriver());
    }

    /**
     * @test
     */
    public function testGetTokenizerReturnsTokenizer()
    {
        $this->assertSame($this->tokenizer, $this->blixt->getTokenizer());
    }

    /**
     * @test
     */
    public function testInstallDoesNotCreateStorageWhenItAlreadyExists()
    {
        $this->storage->shouldReceive('exists')->once()->andReturn(true);
        $this->storage->shouldNotReceive('create');

        $this->assertTrue($this->blixt->install());
    }

    /**
     * @test
     */
    public function testInstallCreatesStorageWhenItDoesNotAlreadyExist()
    {
        $this->storage->shouldReceive('exists')->once()->andReturn(false);
        $this->storage->shouldReceive('install')->once()->andReturn(true);

        $this->assertTrue($this->blixt->install());
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testOpeningExistingSchemaReturnsIndex()
    {
        $schema = Schema::make(1, 'test');
        $schema->setColumns(Collection::make([
            Column::make(1, 1, 'test', false, false)
        ]));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => 'test'], 0, 1])
            ->andReturn([new Record(1, [SchemaRepository::NAME => 'test'])]);

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([ColumnRepository::TABLE, [ColumnRepository::SCHEMA_ID => $schema->getId()], 0, null])
            ->andReturn([new Record(1, [
                ColumnRepository::SCHEMA_ID => 1,
                ColumnRepository::NAME => 'test',
                ColumnRepository::IS_INDEXED => false,
                ColumnRepository::IS_STORED => false
            ])]);

        $index = $this->blixt->open('test');
        $actualSchema = $this->getInaccessibleProperty($index, 'schema');

        $this->assertInstanceOf(Index::class, $index);
        $this->assertEquals($actualSchema, $schema);
        $this->assertEquals($schema->getColumns(), $actualSchema->getColumns());
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testOpeningNonExistentSchemaThrowsException()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => 'test'], 0, 1])
            ->andReturn([]);

        $this->expectException(SchemaDoesNotExistException::class);
        $this->blixt->open('test');
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\SchemaDoesNotExistException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testOpeningNonExistentSchemaWithClosureReturnsIndex()
    {
        $expectedSchema = Schema::make(1, 'schema');
        $expectedSchema->setColumns(Collection::make([
            $expectedColumn = Column::make(1, 1, 'columns', true, false)
        ]));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $expectedSchema->getName()], 0, 1])
            ->andReturn([]);

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $expectedSchema->getName()]])
            ->andReturn(new Record($expectedSchema->getId(), [
                SchemaRepository::NAME => $expectedSchema->getName()
            ]));

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([ColumnRepository::TABLE, [
                ColumnRepository::SCHEMA_ID => $expectedSchema->getId(),
                ColumnRepository::NAME => $expectedColumn->getName(),
                ColumnRepository::IS_INDEXED => $expectedColumn->isIndexed(),
                ColumnRepository::IS_STORED => $expectedColumn->isStored()
            ]])
            ->andReturn(new Record($expectedSchema->getId(), [
                ColumnRepository::SCHEMA_ID => $expectedSchema->getId(),
                ColumnRepository::NAME => $expectedColumn->getName(),
                ColumnRepository::IS_INDEXED => $expectedColumn->isIndexed(),
                ColumnRepository::IS_STORED => $expectedColumn->isStored()
            ]));

        $index = $this->blixt->open($expectedSchema->getName(), function (Blueprint $blueprint) use ($expectedColumn) {
            $blueprint->createDefinition(
                $expectedColumn->getName(),
                $expectedColumn->isIndexed(),
                $expectedColumn->isStored()
            );
        });

        $actualSchema = $this->getInaccessibleProperty($index, 'schema');

        $this->assertInstanceOf(Index::class, $index);
        $this->assertEquals($expectedSchema, $actualSchema);
        $this->assertEquals($expectedSchema->getColumns(), $actualSchema->getColumns());
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\StorageException
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     */
    public function testCreatingSchemaReturnsIndex()
    {
        $expectedSchema = Schema::make(1, 'schema');
        $expectedSchema->setColumns(Collection::make([
            $expectedColumn = Column::make(1, 1, 'columns', true, false)
        ]));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $expectedSchema->getName()], 0, 1])
            ->andReturn([]);

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $expectedSchema->getName()]])
            ->andReturn(new Record($expectedSchema->getId(), [
                SchemaRepository::NAME => $expectedSchema->getName()
            ]));

        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([ColumnRepository::TABLE, [
                ColumnRepository::SCHEMA_ID => $expectedSchema->getId(),
                ColumnRepository::NAME => $expectedColumn->getName(),
                ColumnRepository::IS_INDEXED => $expectedColumn->isIndexed(),
                ColumnRepository::IS_STORED => $expectedColumn->isStored()
            ]])
            ->andReturn(new Record($expectedSchema->getId(), [
                ColumnRepository::SCHEMA_ID => $expectedSchema->getId(),
                ColumnRepository::NAME => $expectedColumn->getName(),
                ColumnRepository::IS_INDEXED => $expectedColumn->isIndexed(),
                ColumnRepository::IS_STORED => $expectedColumn->isStored()
            ]));

        $index = $this->blixt->create(new Blueprint($expectedSchema->getName(), new Collection([
            new Definition($expectedColumn->getName(), $expectedColumn->isIndexed(), $expectedColumn->isStored())
        ])));

        $actualSchema = $this->getInaccessibleProperty($index, 'schema');

        $this->assertInstanceOf(Index::class, $index);
        $this->assertEquals($expectedSchema, $actualSchema);
        $this->assertEquals($expectedSchema->getColumns(), $actualSchema->getColumns());
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\StorageException
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     */
    public function testCreatingSchemaThatAlreadyExistsThrowsException()
    {
        $schema = $schema = new Schema(1, 'test');

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $schema->getName()], 0, 1])
            ->andReturn([new Record($schema->getId(), [
                SchemaRepository::NAME => $schema->getName()
            ])]);

        $this->expectException(IndexAlreadyExistsException::class);
        $this->blixt->create(new Blueprint('test', new Collection([
            new Definition('test_field', true, false)
        ])));
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\InvalidBlueprintException
     * @throws \Blixt\Exceptions\InvalidSchemaException
     * @throws \Blixt\Exceptions\StorageException
     * @throws \Blixt\Exceptions\IndexAlreadyExistsException
     */
    public function testCreatingSchemaWithoutColumnDefinitionsThrowsException()
    {
        $name = 'test';

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => $name], 0, 1])
            ->andReturn([]);

        $this->expectException(InvalidBlueprintException::class);
        $this->blixt->create(new Blueprint($name));
    }
}