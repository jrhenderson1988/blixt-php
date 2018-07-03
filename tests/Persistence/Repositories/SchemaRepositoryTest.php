<?php

namespace BlixtTests\Persistence\Repositories;

use Blixt\Persistence\Drivers\Driver;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\SchemaRepository;
use BlixtTests\TestCase;
use Mockery as m;

class SchemaRepositoryTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Driver
     */
    protected $storage;

    /**
     * @var \Blixt\Persistence\Repositories\SchemaRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->storage = m::mock(Driver::class);
        $this->repository = new SchemaRepository($this->storage);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\SchemaRepository::findByName()
     */
    public function testFindByName()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => 'test'], 0, 1])
            ->andReturn([new Record(1, [SchemaRepository::NAME => 'test'])]);

        $this->assertEquals(Schema::make(1, 'test'), $this->repository->findByName('test'));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([SchemaRepository::TABLE, [SchemaRepository::NAME => 'missing'], 0, 1])
            ->andReturn([]);

        $this->assertNull($this->repository->findByName('missing'));
    }
}