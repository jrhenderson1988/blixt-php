<?php

namespace BlixtTests;

use Blixt\Blixt;
use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Exceptions\InvalidBlueprintException;
use Blixt\Exceptions\StorageException;
use Blixt\Index\Index;
use Blixt\Index\Blueprint\Blueprint;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Drivers\Memory\Storage as MemoryStorage;
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
    /**
     * @test
     */
    public function testInstallDoesNotCreateStorageWhenItAlreadyExists()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);

        $storage->shouldReceive('exists')->andReturn(true);
        $storage->shouldNotReceive('create');

        $blixt = new Blixt($storage, $tokenizer);
        $blixt->install();
    }

    /**
     * @test
     */
    public function testInstallCreatesStorageWhenItDoesNotAlreadyExist()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);

        $storage->shouldReceive('exists')->andReturn(false);
        $storage->shouldReceive('create')->andReturn(true);

        $blixt = new Blixt($storage, $tokenizer);

        $this->assertTrue($blixt->install());
    }

    /**
     * @test
     */
    public function testOpeningNonExistentSchemaThrowsException()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);
        $schemaRepo = m::mock(SchemaRepository::class);
        $columnRepo = m::mock(ColumnRepository::class);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection());
        $columnRepo->shouldReceive('all')->andReturn(new Collection());

        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage, $tokenizer);

        $this->expectException(IndexDoesNotExistException::class);
        $blixt->open('test');
    }

    /** @test */
    public function testOpeningExistingSchemaReturnsIndex()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);
        $schemaRepo = m::mock(SchemaRepository::class);
        $columnRepo = m::mock(ColumnRepository::class);

        $columnRepo->shouldReceive('all')->andReturn(new Collection());
        $schemaRepo->shouldReceive('all')->andReturn(new Collection([
            new Schema(1, 'test')
        ]));

        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage, $tokenizer);
        $indexByName = $blixt->open('test');
        $indexById = $blixt->open(1);

        $this->assertInstanceOf(Index::class, $indexByName);
        $this->assertInstanceOf(Index::class, $indexById);
        $this->assertEquals($indexByName, $indexById);
    }

    /** @test */
    public function testCreatingSchemaReturnsIndex()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);
        $schemaRepo = m::mock(SchemaRepository::class);
        $columnRepo = m::mock(ColumnRepository::class);

        $schema = new Schema(1, 'test');
        $column = new Column(1, 1, 'test', true, false);

        $storage->shouldReceive('columns')->andReturn($columnRepo);
        $columnRepo->shouldReceive('all')->twice()->andReturn(new Collection(), new Collection([$column]));
        $columnRepo->shouldReceive('save')->andReturn($column);

        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $schemaRepo->shouldReceive('all')->andReturn(new Collection(), new Collection([$schema]));
        $schemaRepo->shouldReceive('save')->andReturn($schema);

        $blixt = new Blixt($storage, $tokenizer);

        $index = $blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addDefinition('test', true, false);
        });

        $this->assertInstanceOf(Index::class, $index);
    }

    /** @test */
    public function testCreatingSchemaThatAlreadyExistsThrowsException()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);
        $schemaRepo = m::mock(SchemaRepository::class);
        $columnRepo = m::mock(ColumnRepository::class);

        $schema = new Schema(1, 'test');
        $column = new Column(1, 1, 'test', true, false);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection([$schema]));
        $columnRepo->shouldReceive('all')->andReturn(new Collection([$column]));
        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $this->expectException(IndexAlreadyExistsException::class);

        $blixt = new Blixt($storage, $tokenizer);
        $blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addDefinition('test', true, false);
        });
    }

    /** @test */
    public function testCreatingSchemaWithoutDefiningColumnsThrowsException()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);
        $schemaRepo = m::mock(SchemaRepository::class);
        $columnRepo = m::mock(ColumnRepository::class);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection());
        $columnRepo->shouldReceive('all')->andReturn(new Collection());
        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage, $tokenizer);

        $this->expectException(InvalidBlueprintException::class);

        $blixt->create('test');
    }

    /** @test */
    public function testExceptionIsThrownWhenStorageIsUnableToCreateSchema()
    {
        $storage = m::mock(Storage::class);
        $tokenizer = m::mock(Tokenizer::class);
        $schemaRepo = m::mock(SchemaRepository::class);
        $columnRepo = m::mock(ColumnRepository::class);

        $name = 'test';
        $schema = (new Schema())->name($name);
        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);
        $schemaRepo->shouldReceive('all')->andReturn(new Collection());
        $schemaRepo->shouldReceive('save')->with(m::on(function ($arg) use ($schema) {
            return $arg == $schema;
        }))->andReturn(null);
        $columnRepo->shouldReceive('all')->andReturn(new Collection());

        $this->expectException(StorageException::class);

        $blixt = new Blixt($storage, $tokenizer);
        $blixt->create($name, function (Blueprint $blueprint) {
            $blueprint->addDefinition('test', true, false);
        });
    }
}