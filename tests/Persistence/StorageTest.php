<?php

namespace BlixtTests\Persistence;

use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Document;
use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Occurrence;
use Blixt\Persistence\Entities\Position;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Entities\Word;
use Blixt\Persistence\Repositories\ColumnRepository;
use Blixt\Persistence\Repositories\DocumentRepository;
use Blixt\Persistence\Repositories\FieldRepository;
use Blixt\Persistence\Repositories\OccurrenceRepository;
use Blixt\Persistence\Repositories\PositionRepository;
use Blixt\Persistence\Repositories\SchemaRepository;
use Blixt\Persistence\Repositories\TermRepository;
use Blixt\Persistence\Repositories\WordRepository;
use Blixt\Persistence\Storage;
use Mockery as m;
use Blixt\Persistence\Drivers\Driver;
use BlixtTests\TestCase;

class StorageTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Driver
     */
    protected $driver;

    /**
     * @var \Blixt\Persistence\Storage
     */
    protected $storage;

    public function setUp()
    {
        $this->driver = m::mock(Driver::class);
        $this->storage = new Storage($this->driver);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Storage::getDriver()
     */
    public function testGetDriverReturnsSameDriver()
    {
        $this->assertEquals($this->driver, $this->storage->getDriver());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Storage::exists()
     */
    public function testExists()
    {
        $this->driver->shouldReceive('exists')->twice()->andReturnValues([false, true]);
        $this->assertFalse($this->driver->exists());
        $this->assertTrue($this->driver->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Storage::install()
     */
    public function testInstall()
    {
        $this->driver->shouldReceive('install')->once()->andReturnTrue();
        $this->assertTrue($this->storage->install());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Storage::repository()
     */
    public function testRepository()
    {
        $this->assertInstanceOf(ColumnRepository::class, $this->storage->repository(ColumnRepository::class));
        $this->assertInstanceOf(ColumnRepository::class, $this->storage->repository(Column::class));
        $this->assertSame(
            $this->storage->repository(ColumnRepository::class),
            $this->storage->repository(Column::class)
        );

        $this->assertInstanceOf(DocumentRepository::class, $this->storage->repository(DocumentRepository::class));
        $this->assertInstanceOf(DocumentRepository::class, $this->storage->repository(Document::class));
        $this->assertSame(
            $this->storage->repository(DocumentRepository::class),
            $this->storage->repository(Document::class)
        );

        $this->assertInstanceOf(FieldRepository::class, $this->storage->repository(FieldRepository::class));
        $this->assertInstanceOf(FieldRepository::class, $this->storage->repository(Field::class));
        $this->assertSame(
            $this->storage->repository(FieldRepository::class),
            $this->storage->repository(Field::class)
        );

        $this->assertInstanceOf(OccurrenceRepository::class, $this->storage->repository(OccurrenceRepository::class));
        $this->assertInstanceOf(OccurrenceRepository::class, $this->storage->repository(Occurrence::class));
        $this->assertSame(
            $this->storage->repository(OccurrenceRepository::class),
            $this->storage->repository(Occurrence::class)
        );

        $this->assertInstanceOf(PositionRepository::class, $this->storage->repository(PositionRepository::class));
        $this->assertInstanceOf(PositionRepository::class, $this->storage->repository(Position::class));
        $this->assertSame(
            $this->storage->repository(PositionRepository::class),
            $this->storage->repository(Position::class)
        );

        $this->assertInstanceOf(SchemaRepository::class, $this->storage->repository(SchemaRepository::class));
        $this->assertInstanceOf(SchemaRepository::class, $this->storage->repository(Schema::class));
        $this->assertSame(
            $this->storage->repository(SchemaRepository::class),
            $this->storage->repository(Schema::class)
        );

        $this->assertInstanceOf(TermRepository::class, $this->storage->repository(TermRepository::class));
        $this->assertInstanceOf(TermRepository::class, $this->storage->repository(Term::class));
        $this->assertSame(
            $this->storage->repository(TermRepository::class),
            $this->storage->repository(Term::class)
        );

        $this->assertInstanceOf(WordRepository::class, $this->storage->repository(WordRepository::class));
        $this->assertInstanceOf(WordRepository::class, $this->storage->repository(Word::class));
        $this->assertSame(
            $this->storage->repository(WordRepository::class),
            $this->storage->repository(Word::class)
        );
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Storage::columns()
     * @covers \Blixt\Persistence\Storage::documents()
     * @covers \Blixt\Persistence\Storage::fields()
     * @covers \Blixt\Persistence\Storage::occurrences()
     * @covers \Blixt\Persistence\Storage::positions()
     * @covers \Blixt\Persistence\Storage::schemas()
     * @covers \Blixt\Persistence\Storage::terms()
     * @covers \Blixt\Persistence\Storage::words()
     */
    public function testConvenienceMethods()
    {
        $this->assertInstanceOf(ColumnRepository::class, $this->storage->columns());
        $this->assertInstanceOf(DocumentRepository::class, $this->storage->documents());
        $this->assertInstanceOf(FieldRepository::class, $this->storage->fields());
        $this->assertInstanceOf(OccurrenceRepository::class, $this->storage->occurrences());
        $this->assertInstanceOf(PositionRepository::class, $this->storage->positions());
        $this->assertInstanceOf(SchemaRepository::class, $this->storage->schemas());
        $this->assertInstanceOf(TermRepository::class, $this->storage->terms());
        $this->assertInstanceOf(WordRepository::class, $this->storage->words());
    }
}