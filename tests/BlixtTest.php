<?php

namespace BlixtTests;

use Blixt\Blixt;
use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Exceptions\SchemaDoesNotExistException;
use Blixt\Exceptions\InvalidBlueprintException;
use Blixt\Exceptions\StorageException;
use Blixt\Index\Blueprint\Definition;
use Blixt\Index\Index;
use Blixt\Index\Blueprint\Blueprint;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\ColumnRepository;
use Blixt\Storage\Repositories\SchemaRepository;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;
use Mockery as m;

class BlixtTest extends TestCase
{
    protected $blixt;
    protected $storage;
    protected $tokenizer;
    protected $schemaRepo;
    protected $columnRepo;

    public function setUp()
    {
        $this->schemaRepo = m::mock(SchemaRepository::class);
        $this->columnRepo = m::mock(ColumnRepository::class);

        $this->blixt = new Blixt(
            $this->storage = m::mock(Storage::class),
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
    public function testGetStorageReturnsStorage()
    {
        $this->assertSame($this->storage, $this->blixt->getStorage());
    }

    /**
     * @test
     */
    public function testGetStorageReturnsTokenizer()
    {
        $this->assertSame($this->tokenizer, $this->blixt->getTokenizer());
    }

    /**
     * @test
     */
    public function testInstallDoesNotCreateStorageWhenItAlreadyExists()
    {
        $this->storage->shouldReceive('exists')->andReturn(true);
        $this->storage->shouldNotReceive('create');

        $this->assertTrue($this->blixt->install());
    }

    /**
     * @test
     */
    public function testInstallCreatesStorageWhenItDoesNotAlreadyExist()
    {
        $this->storage->shouldReceive('exists')->andReturn(false);
        $this->storage->shouldReceive('create')->andReturn(true);

        $this->assertTrue($this->blixt->install());
    }

    /**
     * @test
     */
    public function testOpeningExistingSchemaReturnsIndex()
    {
        $schema = new Schema(1, 'test');
        $schema->setColumns(Collection::make([
            Column::make(1, 1, 'test', false, false)
        ]));

        $this->schemaRepo->shouldReceive('findByName')->withArgs(['test'])->andReturn($schema);
        $this->columnRepo->shouldReceive('getBySchema')->with(m::on(function ($arg) use ($schema) {
            return $arg == $schema;
        }))->andReturn(new Collection());
        $this->storage->shouldReceive('schemas')->andReturn($this->schemaRepo);
        $this->storage->shouldReceive('columns')->andReturn($this->columnRepo);

        $index = $this->blixt->open('test');
        $this->assertInstanceOf(Index::class, $index);
        $this->assertSame($this->getInaccessibleProperty($index, 'schema'), $schema);
    }

    /**
     * @test
     */
    public function testOpeningNonExistentSchemaThrowsException()
    {
        $this->storage->shouldReceive('schemas')->andReturn($this->schemaRepo);
        $this->schemaRepo->shouldReceive('findByName')->withArgs(['test'])->andReturn(null);

        $this->expectException(SchemaDoesNotExistException::class);
        $this->blixt->open('test');
    }

    /**
     * @test
     */
    public function testOpeningNonExistentSchemaWithClosureReturnsIndex()
    {
        $this->storage->shouldReceive('schemas')->andReturn($this->schemaRepo);
        $this->schemaRepo->shouldReceive('findByName')->withArgs(['test'])->andReturn(null);
        $this->schemaRepo->shouldReceive('save')->with(m::on(function ($arg) {
            return $arg == Schema::create('test');
        }))->andReturn($schema = Schema::make(1, 'test'));

        $this->storage->shouldReceive('columns')->andReturn($this->columnRepo);
        $this->columnRepo->shouldReceive('save')->with(m::on(function ($arg) use ($schema) {
            return $arg == Column::create($schema->getId(), 'test_field', true, false);
        }))->andReturn($column = Column::make(1, $schema->getId(), 'test_field', true, false));

        $index = $this->blixt->open('test', function (Blueprint $blueprint) {
            $blueprint->createDefinition('test_field', true, false);
        });

        $this->assertInstanceOf(Index::class, $index);
        $this->assertSame($this->getInaccessibleProperty($index, 'schema'), $schema);
    }

    /**
     * @test
     */
    public function testCreatingSchemaReturnsIndex()
    {
        $this->storage->shouldReceive('schemas')->andReturn($this->schemaRepo);
        $this->schemaRepo->shouldReceive('findByName')->withArgs(['test'])->andReturn(null);
        $this->schemaRepo->shouldReceive('save')->with(m::on(function ($arg) {
            return $arg == Schema::create('test');
        }))->andReturn($schema = Schema::make(1, 'test'));

        $this->storage->shouldReceive('columns')->andReturn($this->columnRepo);
        $this->columnRepo->shouldReceive('save')->with(m::on(function ($arg) use ($schema) {
            return $arg == Column::create($schema->getId(), 'test_field', true, false);
        }))->andReturn($column = Column::make(1, $schema->getId(), 'test_field', true, false));

        $index = $this->blixt->create(new Blueprint('test', new Collection([
            new Definition('test_field', true, false)
        ])));

        $this->assertInstanceOf(Index::class, $index);
        $this->assertSame($this->getInaccessibleProperty($index, 'schema'), $schema);
    }

    /**
     * @test
     */
    public function testCreatingSchemaThatAlreadyExistsThrowsException()
    {
        $schema = new Schema(1, 'test');
        $this->storage->shouldReceive('schemas')->andReturn($this->schemaRepo);
        $this->schemaRepo->shouldReceive('findByName')->andReturn($schema);

        $this->expectException(IndexAlreadyExistsException::class);
        $this->blixt->create(new Blueprint('test', new Collection([
            new Definition('test_field', true, false)
        ])));
    }

    /**
     * @test
     */
    public function testCreatingSchemaWithoutColumnDefinitionsThrowsException()
    {
        $this->storage->shouldReceive('schemas')->andReturn($this->schemaRepo);
        $this->schemaRepo->shouldReceive('findByName')->andReturn(null);

        $this->expectException(InvalidBlueprintException::class);
        $this->blixt->create(new Blueprint('test'));
    }

    /** @test */
    public function testExceptionIsThrownWhenStorageIsUnableToCreateSchema()
    {
        $this->storage->shouldReceive('schemas')->andReturn($this->schemaRepo);
        $this->schemaRepo->shouldReceive('findByName')->andReturn(null);
        $this->schemaRepo->shouldReceive('save')->with(m::on(function ($arg) {
            return $arg == Schema::create('test');
        }))->andReturn(null);

        $this->expectException(StorageException::class);
        $this->blixt->create(new Blueprint('test', new Collection([
            new Definition('test_field', true, false)
        ])));
    }
}