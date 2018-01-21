<?php

namespace BlixtTests;

use Blixt\Blixt;
use Blixt\Exceptions\IndexAlreadyExistsException;
use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Exceptions\InvalidBlueprintException;
use Blixt\Exceptions\StorageException;
use Blixt\Index\Index;
use Blixt\Index\Schema\Blueprint;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Repositories\ColumnRepository;
use Blixt\Storage\Repositories\SchemaRepository;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;
use Mockery;

class BlixtTest extends TestCase
{

    /** @test  */
    public function testBlixtInstallCreatesSchemasWhenTheyDoNotExist()
    {
        // Ensure create is called when exists return false.
        $storage = Mockery::mock(Storage::class);
        $storage->shouldReceive('exists')->andReturn(false);
        $storage->shouldReceive('create')->andReturn(true);
        $this->assertTrue(Blixt::install($storage));
    }

    /** @test  */
    public function testBlixtInstallDoesNotCreatesSchemasWhenTheyExist()
    {
        $storage = Mockery::mock(Storage::class);
        $storage->shouldReceive('exists')->andReturn(true);
        $storage->shouldNotReceive('create');
        $this->assertTrue(Blixt::install($storage));
    }

    /** @test */
    public function testCreatingBlixtObjectWithoutProvidingStemmerAndTokenizerCreatesSensibleDefaults()
    {
        Blixt::install($storage = new \Blixt\Storage\Drivers\Memory\Storage());

        $blixt = new Blixt($storage);
        $this->assertInstanceOf(Stemmer::class, $blixt->getStemmer());
        $this->assertInstanceOf(Tokenizer::class, $blixt->getTokenizer());
    }

    /** @test */
    public function testCreatingBlixtObjectLoadsSchemas()
    {
        $storage = Mockery::mock(Storage::class);
        $schemaRepo = Mockery::mock(SchemaRepository::class);
        $columnRepo = Mockery::mock(ColumnRepository::class);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection());
        $columnRepo->shouldReceive('all')->andReturn(new Collection());

        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        new Blixt($storage);
    }

    /** @test */
    public function testOpeningNonExistentSchemaThrowsException()
    {
        $storage = Mockery::mock(Storage::class);
        $schemaRepo = Mockery::mock(SchemaRepository::class);
        $columnRepo = Mockery::mock(ColumnRepository::class);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection());
        $columnRepo->shouldReceive('all')->andReturn(new Collection());

        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage);

        $this->expectException(IndexDoesNotExistException::class);
        $blixt->open('test');
    }

    /** @test */
    public function testOpeningExistingSchemaReturnsIndex()
    {
        $storage = Mockery::mock(Storage::class);
        $schemaRepo = Mockery::mock(SchemaRepository::class);
        $columnRepo = Mockery::mock(ColumnRepository::class);

        $columnRepo->shouldReceive('all')->andReturn(new Collection());
        $schemaRepo->shouldReceive('all')->andReturn(new Collection([
            new Schema(1, 'test')
        ]));

        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage);
        $indexByName = $blixt->open('test');
        $indexById = $blixt->open(1);

        $this->assertInstanceOf(Index::class, $indexByName);
        $this->assertInstanceOf(Index::class, $indexById);
        $this->assertEquals($indexByName, $indexById);
    }

    /** @test */
    public function testCreatingSchemaReturnsIndex()
    {
        $storage = Mockery::mock(Storage::class);
        $schemaRepo = Mockery::mock(SchemaRepository::class);
        $columnRepo = Mockery::mock(ColumnRepository::class);

        $schema = new Schema(1, 'test');
        $column = new Column(1, 1, 'test', true, false);

        $schemaRepo->shouldReceive('all')->twice()->andReturn(new Collection(), new Collection([$schema]));
        $columnRepo->shouldReceive('all')->twice()->andReturn(new Collection(), new Collection([$column]));
        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage);

        $schemaRepo->shouldReceive('create')->withArgs(['test'])->andReturn($schema);
        $columnRepo->shouldReceive('create')->withArgs([1, 'test', true, false])->andReturn($column);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection([$schema]));
        $columnRepo->shouldReceive('all')->andReturn(new Collection([$column]));

        $index = $blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addColumnDefinition('test', true, false);
        });

        $this->assertInstanceOf(Index::class, $index);
    }

    /** @test */
    public function testCreatingSchemaThatAlreadyExistsThrowsException()
    {
        $storage = Mockery::mock(Storage::class);
        $schemaRepo = Mockery::mock(SchemaRepository::class);
        $columnRepo = Mockery::mock(ColumnRepository::class);

        $schema = new Schema(1, 'test');
        $column = new Column(1, 1, 'test', true, false);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection([$schema]));
        $columnRepo->shouldReceive('all')->andReturn(new Collection([$column]));
        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $this->expectException(IndexAlreadyExistsException::class);

        $blixt = new Blixt($storage);
        $blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addColumnDefinition('test', true, false);
        });
    }

    /** @test */
    public function testCreatingSchemaWithoutDefiningColumnsThrowsException()
    {
        $storage = Mockery::mock(Storage::class);
        $schemaRepo = Mockery::mock(SchemaRepository::class);
        $columnRepo = Mockery::mock(ColumnRepository::class);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection());
        $columnRepo->shouldReceive('all')->andReturn(new Collection());
        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage);

        $this->expectException(InvalidBlueprintException::class);

        $blixt->create('test');
    }

    /** @test */
    public function testExceeptionIsThrownWhenStorageIsUnableToCreateSchema()
    {
        $storage = Mockery::mock(Storage::class);
        $schemaRepo = Mockery::mock(SchemaRepository::class);
        $columnRepo = Mockery::mock(ColumnRepository::class);

        $schemaRepo->shouldReceive('all')->andReturn(new Collection());
        $columnRepo->shouldReceive('all')->andReturn(new Collection());
        $storage->shouldReceive('schemas')->andReturn($schemaRepo);
        $storage->shouldReceive('columns')->andReturn($columnRepo);

        $blixt = new Blixt($storage);

        $schemaRepo->shouldReceive('create')->withArgs(['test'])->andReturn(null);

        $this->expectException(StorageException::class);

        $blixt->create('test', function (Blueprint $blueprint) {
            $blueprint->addColumnDefinition('test', true, false);
        });
    }
}