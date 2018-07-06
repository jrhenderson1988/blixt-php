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
use Blixt\Persistence\StorageManager;
use Mockery as m;
use Blixt\Persistence\Drivers\Storage;
use BlixtTests\TestCase;

class StorageManagerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Storage
     */
    protected $driver;

    /**
     * @var \Blixt\Persistence\StorageManager
     */
    protected $manager;

    public function setUp()
    {
        $this->driver = m::mock(Storage::class);
        $this->manager = new StorageManager($this->driver);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\StorageManager::getDriver()
     */
    public function testGetDriverReturnsSameDriver()
    {
        $this->assertEquals($this->driver, $this->manager->getDriver());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\StorageManager::exists()
     */
    public function testExists()
    {
        $this->driver->shouldReceive('exists')->twice()->andReturnValues([false, true]);
        $this->assertFalse($this->driver->exists());
        $this->assertTrue($this->driver->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\StorageManager::install()
     */
    public function testInstall()
    {
        $this->driver->shouldReceive('install')->once()->andReturnTrue();
        $this->assertTrue($this->manager->install());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\StorageManager::repository()
     */
    public function testRepository()
    {
        $this->assertInstanceOf(ColumnRepository::class, $this->manager->repository(ColumnRepository::class));
        $this->assertInstanceOf(ColumnRepository::class, $this->manager->repository(Column::class));
        $this->assertSame(
            $this->manager->repository(ColumnRepository::class),
            $this->manager->repository(Column::class)
        );

        $this->assertInstanceOf(DocumentRepository::class, $this->manager->repository(DocumentRepository::class));
        $this->assertInstanceOf(DocumentRepository::class, $this->manager->repository(Document::class));
        $this->assertSame(
            $this->manager->repository(DocumentRepository::class),
            $this->manager->repository(Document::class)
        );

        $this->assertInstanceOf(FieldRepository::class, $this->manager->repository(FieldRepository::class));
        $this->assertInstanceOf(FieldRepository::class, $this->manager->repository(Field::class));
        $this->assertSame(
            $this->manager->repository(FieldRepository::class),
            $this->manager->repository(Field::class)
        );

        $this->assertInstanceOf(OccurrenceRepository::class, $this->manager->repository(OccurrenceRepository::class));
        $this->assertInstanceOf(OccurrenceRepository::class, $this->manager->repository(Occurrence::class));
        $this->assertSame(
            $this->manager->repository(OccurrenceRepository::class),
            $this->manager->repository(Occurrence::class)
        );

        $this->assertInstanceOf(PositionRepository::class, $this->manager->repository(PositionRepository::class));
        $this->assertInstanceOf(PositionRepository::class, $this->manager->repository(Position::class));
        $this->assertSame(
            $this->manager->repository(PositionRepository::class),
            $this->manager->repository(Position::class)
        );

        $this->assertInstanceOf(SchemaRepository::class, $this->manager->repository(SchemaRepository::class));
        $this->assertInstanceOf(SchemaRepository::class, $this->manager->repository(Schema::class));
        $this->assertSame(
            $this->manager->repository(SchemaRepository::class),
            $this->manager->repository(Schema::class)
        );

        $this->assertInstanceOf(TermRepository::class, $this->manager->repository(TermRepository::class));
        $this->assertInstanceOf(TermRepository::class, $this->manager->repository(Term::class));
        $this->assertSame(
            $this->manager->repository(TermRepository::class),
            $this->manager->repository(Term::class)
        );

        $this->assertInstanceOf(WordRepository::class, $this->manager->repository(WordRepository::class));
        $this->assertInstanceOf(WordRepository::class, $this->manager->repository(Word::class));
        $this->assertSame(
            $this->manager->repository(WordRepository::class),
            $this->manager->repository(Word::class)
        );
    }

    /**
     * @test
     * @covers \Blixt\Persistence\StorageManager::columns()
     * @covers \Blixt\Persistence\StorageManager::documents()
     * @covers \Blixt\Persistence\StorageManager::fields()
     * @covers \Blixt\Persistence\StorageManager::occurrences()
     * @covers \Blixt\Persistence\StorageManager::positions()
     * @covers \Blixt\Persistence\StorageManager::schemas()
     * @covers \Blixt\Persistence\StorageManager::terms()
     * @covers \Blixt\Persistence\StorageManager::words()
     */
    public function testConvenienceMethods()
    {
        $this->assertInstanceOf(ColumnRepository::class, $this->manager->columns());
        $this->assertInstanceOf(DocumentRepository::class, $this->manager->documents());
        $this->assertInstanceOf(FieldRepository::class, $this->manager->fields());
        $this->assertInstanceOf(OccurrenceRepository::class, $this->manager->occurrences());
        $this->assertInstanceOf(PositionRepository::class, $this->manager->positions());
        $this->assertInstanceOf(SchemaRepository::class, $this->manager->schemas());
        $this->assertInstanceOf(TermRepository::class, $this->manager->terms());
        $this->assertInstanceOf(WordRepository::class, $this->manager->words());
    }
}
