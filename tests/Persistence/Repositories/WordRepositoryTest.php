<?php

namespace BlixtTests\Persistence\Repositories;

use Blixt\Persistence\Drivers\Storage;
use Blixt\Persistence\Entities\Word;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\WordRepository;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use Mockery as m;

class WordRepositoryTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Persistence\Repositories\WordRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->storage = m::mock(Storage::class);
        $this->repository = new WordRepository($this->storage);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\WordRepository::findByWord()
     */
    public function testFindByWord()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, [WordRepository::WORD => 'foo'], 0, 1])
            ->andReturn([
                new Record(1, [WordRepository::WORD => 'foo'])
            ]);

        $this->assertEquals(Word::make(1, 'foo'), $this->repository->findByWord('foo'));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, [WordRepository::WORD => 'bar'], 0, 1])
            ->andReturn([]);

        $this->assertNull($this->repository->findByWord('bar'));
    }

    public function testGetByWords()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, [WordRepository::WORD => ['foo', 'bar']], 0, null])
            ->andReturn([
                new Record(1, [WordRepository::WORD => 'foo']),
                new Record(2, [WordRepository::WORD => 'bar'])
            ]);

        $this->assertEquals(
            Collection::make([1 => Word::make(1, 'foo'), 2 => Word::make(2, 'bar')]),
            $this->repository->getByWords(Collection::make(['foo', 'bar']))
        );

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([WordRepository::TABLE, [WordRepository::WORD => ['missing', 'words']], 0, null])
            ->andReturn([]);

        $this->assertEquals(
            Collection::make([]),
            $this->repository->getByWords(Collection::make(['missing', 'words']))
        );
    }
}
