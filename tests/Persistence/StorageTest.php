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
     */
    public function testCorrectRepositoriesAreReturnedUsingConvenienceMethods()
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

    /**
     * @test
     */
    public function testCorrectRepositoriesAreReturnedUsingRepositoryMethodWithEntityClassName()
    {
        $this->assertInstanceOf(ColumnRepository::class, $this->storage->repository(Column::class));
        $this->assertInstanceOf(DocumentRepository::class, $this->storage->repository(Document::class));
        $this->assertInstanceOf(FieldRepository::class, $this->storage->repository(Field::class));
        $this->assertInstanceOf(OccurrenceRepository::class, $this->storage->repository(Occurrence::class));
        $this->assertInstanceOf(PositionRepository::class, $this->storage->repository(Position::class));
        $this->assertInstanceOf(SchemaRepository::class, $this->storage->repository(Schema::class));
        $this->assertInstanceOf(TermRepository::class, $this->storage->repository(Term::class));
        $this->assertInstanceOf(WordRepository::class, $this->storage->repository(Word::class));
    }
}