<?php

namespace BlixtTests\Persistence;

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
     * @test
     */
    public function testCorrectRepositoriesAreReturned()
    {
        $storage = new Storage($driver = m::mock(Driver::class));

        $this->assertInstanceOf(ColumnRepository::class, $storage->columns());
        $this->assertInstanceOf(DocumentRepository::class, $storage->documents());
        $this->assertInstanceOf(FieldRepository::class, $storage->fields());
        $this->assertInstanceOf(OccurrenceRepository::class, $storage->occurrences());
        $this->assertInstanceOf(PositionRepository::class, $storage->positions());
        $this->assertInstanceOf(SchemaRepository::class, $storage->schemas());
        $this->assertInstanceOf(TermRepository::class, $storage->terms());
        $this->assertInstanceOf(WordRepository::class, $storage->words());
    }
}