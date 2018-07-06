<?php

namespace BlixtTests\Persistence\Repositories;

use Blixt\Persistence\Drivers\Storage;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Entities\Word;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\TermRepository;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use Mockery as m;

class TermRepositoryTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Persistence\Repositories\TermRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->storage = m::mock(Storage::class);
        $this->repository = new TermRepository($this->storage);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\TermRepository::findBySchemaAndWord()
     */
    public function testFindBySchemaAndWord()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, [TermRepository::SCHEMA_ID => 1, TermRepository::WORD_ID => 2], 0, 1])
            ->andReturn([new Record(1, [
                TermRepository::SCHEMA_ID => 1,
                TermRepository::WORD_ID => 2,
                TermRepository::FIELD_COUNT => 3
            ])]);

        $this->assertEquals(
            Term::make(1, 1, 2, 3),
            $this->repository->findBySchemaAndWord(new Schema(1, 'test'), new Word(2, 'test'))
        );

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TermRepository::TABLE, [TermRepository::SCHEMA_ID => 2, TermRepository::WORD_ID => 3], 0, 1])
            ->andReturn([]);

        $this->assertNull($this->repository->findBySchemaAndWord(new Schema(2, 'missing'), new Word(3, 'missing')));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\TermRepository::getBySchemaAndWords()
     */
    public function testGetBySchemaAndWords()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([
                TermRepository::TABLE,
                [TermRepository::SCHEMA_ID => 1, TermRepository::WORD_ID => [2, 3]],
                0,
                null
            ])
            ->andReturn([new Record(1, [
                TermRepository::SCHEMA_ID => 1,
                TermRepository::WORD_ID => 2,
                TermRepository::FIELD_COUNT => 3
            ]), new Record(2, [
                TermRepository::SCHEMA_ID => 1,
                TermRepository::WORD_ID => 3,
                TermRepository::FIELD_COUNT => 5
            ])]);

        $this->assertEquals(
            Collection::make([Term::make(1, 1, 2, 3), Term::make(2, 1, 3, 5)]),
            $this->repository->getBySchemaAndWords(
                new Schema(1, 'test'),
                Collection::make([new Word(2, 'foo'), new Word(3, 'bar')])
            )
        );

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([
                TermRepository::TABLE,
                [TermRepository::SCHEMA_ID => 2, TermRepository::WORD_ID => [4, 5]],
                0,
                null
            ])
            ->andReturn([]);

        $this->assertEquals(Collection::make([]), $this->repository->getBySchemaAndWords(
            new Schema(2, 'missing'),
            Collection::make([new Word(4, 'foo'), new Word(5, 'bar')])
        ));
    }
}
