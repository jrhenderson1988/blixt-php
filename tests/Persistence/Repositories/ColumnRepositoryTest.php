<?php

namespace BlixtTests\Persistence\Repositories;

use Blixt\Persistence\Drivers\Storage;
use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\ColumnRepository;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use Mockery as m;

class ColumnRepositoryTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Drivers\Storage|\Mockery\MockInterface
     */
    protected $storage;

    /**
     * @var \Blixt\Persistence\Repositories\ColumnRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->storage = m::mock(Storage::class);
        $this->repository = new ColumnRepository($this->storage);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\ColumnRepository::getBySchema()
     */
    public function testGetBySchema()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([ColumnRepository::TABLE, [ColumnRepository::SCHEMA_ID => 1], 0, null])
            ->andReturn([new Record(1, [
                ColumnRepository::SCHEMA_ID => 1,
                ColumnRepository::NAME => 'test',
                ColumnRepository::IS_INDEXED => true,
                ColumnRepository::IS_STORED => false
            ])]);

        $this->assertEquals(
            Collection::make([Column::make(1, 1, 'test', true, false)]),
            $this->repository->getBySchema(new Schema(1, 'test'))
        );

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([ColumnRepository::TABLE, [ColumnRepository::SCHEMA_ID => 1234], 0, null])
            ->andReturn([]);

        $this->assertEquals(
            Collection::make([]),
            $this->repository->getBySchema(new Schema(1234, 'test'))
        );
    }
}