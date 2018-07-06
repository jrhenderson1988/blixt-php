<?php

namespace BlixtTests\Persistence\Repositories;

use Blixt\Persistence\Drivers\Storage;
use Blixt\Persistence\Entities\Document;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\DocumentRepository;
use BlixtTests\TestCase;
use Mockery as m;

class DocumentRepositoryTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Persistence\Repositories\DocumentRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->storage = m::mock(Storage::class);
        $this->repository = new DocumentRepository($this->storage);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\DocumentRepository::getBySchema()
     */
    public function testFindBySchemaAndKey()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([
                DocumentRepository::TABLE,
                [DocumentRepository::SCHEMA_ID => 1, DocumentRepository::KEY => 1],
                0,
                1
            ])
            ->andReturn([new Record(1, [
                DocumentRepository::SCHEMA_ID => 1,
                DocumentRepository::KEY => 1
            ])]);

        $this->assertEquals(Document::make(1, 1, 1), $this->repository->findBySchemaAndKey(new Schema(1, 'test'), 1));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([DocumentRepository::TABLE, [DocumentRepository::SCHEMA_ID => 1234, DocumentRepository::KEY => 1], 0, 1])
            ->andReturn([]);

        $this->assertNull($this->repository->findBySchemaAndKey(new Schema(1234, 'test'), 1));
    }
}
