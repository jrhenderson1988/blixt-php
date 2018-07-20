<?php

namespace BlixtTests\Persistence\Repositories;

use Blixt\Persistence\Drivers\Storage;
use Blixt\Persistence\Entities\Entity;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\AbstractRepository;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use Mockery as m;

class AbstractRepositoryTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Blixt\Persistence\Drivers\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Persistence\Repositories\AbstractRepository
     */
    protected $repository;

    public function setUp()
    {
        $this->storage = m::mock(Storage::class);
        $this->repository = new TestRepository($this->storage);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::toAttributes()
     */
    public function testToAttributes()
    {
        $this->assertEquals(
            [TestRepository::NAME => 'test'],
            TestRepository::toAttributes(TestEntity::make(1, 'test'))
        );
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::toEntity()
     */
    public function testToEntity()
    {
        $this->assertEquals(
            TestEntity::make(1, 'test'),
            TestRepository::toEntity(1, [TestRepository::NAME => 'test'])
        );
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::create()
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testCreate()
    {
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TestRepository::TABLE, [TestRepository::NAME => 'test']])
            ->andReturn(new Record(1, [TestRepository::NAME => 'test']));

        $this->assertEquals(TestEntity::make(1, 'test'), $this->repository->create(TestEntity::create('test')));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::update()
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testUpdate()
    {
        $this->storage->shouldReceive('update')
            ->once()
            ->withArgs([TestRepository::TABLE, 1, [TestRepository::NAME => 'test']])
            ->andReturn(new Record(1, [TestRepository::NAME => 'test']));

        $this->assertEquals(TestEntity::make(1, 'test'), $this->repository->update(TestEntity::make(1, 'test')));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::save()
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testSaveOnExistingRecord()
    {
        $this->storage->shouldReceive('update')
            ->once()
            ->withArgs([TestRepository::TABLE, 1, [TestRepository::NAME => 'test']])
            ->andReturn(new Record(1, [TestRepository::NAME => 'test']));

        $this->storage->shouldNotReceive('create');

        $this->assertEquals(TestEntity::make(1, 'test'), $this->repository->save(TestEntity::make(1, 'test')));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::save()
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testSaveOnNewRecord()
    {
        $this->storage->shouldReceive('create')
            ->once()
            ->withArgs([TestRepository::TABLE, [TestRepository::NAME => 'test']])
            ->andReturn(new Record(1, [TestRepository::NAME => 'test']));

        $this->storage->shouldNotReceive('update');

        $this->assertEquals(TestEntity::make(1, 'test'), $this->repository->save(TestEntity::create('test')));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::findBy()
     */
    public function testFindBy()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TestRepository::TABLE, [TestRepository::NAME => 1], 0, 1])
            ->andReturn([new Record(1, [TestRepository::NAME => 'test'])]);

        $this->assertEquals(TestEntity::make(1, 'test'), $this->repository->findBy([TestRepository::NAME => 1]));

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TestRepository::TABLE, [TestRepository::NAME => 1234], 0, 1])
            ->andReturn([]);

        $this->assertNull($this->repository->findBy([TestRepository::NAME => 1234]));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::get()
     */
    public function testGet()
    {
        $this->storage->shouldReceive('get')
            ->once()
            ->withArgs([TestRepository::TABLE, [1, 2, 3]])
            ->andReturn([
                new Record(1, [TestRepository::NAME => 'one']),
                new Record(2, [TestRepository::NAME => 'two']),
                new Record(3, [TestRepository::NAME => 'three']),
            ]);

        $this->assertEquals(
            Collection::make([
                1 => TestEntity::make(1, 'one'),
                2 => TestEntity::make(2, 'two'),
                3 => TestEntity::make(3, 'three')
            ]),
            $this->repository->get([1, 2, 3])
        );

        $this->storage->shouldReceive('get')
            ->once()
            ->withArgs([TestRepository::TABLE, [4]])
            ->andReturn([]);

        $this->assertEquals(Collection::make([]), $this->repository->get([4]));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::find()
     */
    public function testFind()
    {
        $this->storage->shouldReceive('get')
            ->once()
            ->withArgs([TestRepository::TABLE, [1]])
            ->andReturn([new Record(1, [TestRepository::NAME => 'test'])]);

        $this->assertEquals(TestEntity::make(1, 'test'), $this->repository->find(1));

        $this->storage->shouldReceive('get')
            ->once()
            ->withArgs([TestRepository::TABLE, [1234]])
            ->andReturn([]);

        $this->assertNull($this->repository->find(1234));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::getWhere()
     */
    public function testGetWhere()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TestRepository::TABLE, [TestRepository::NAME => 1], 0, 1])
            ->andReturn([new Record(1, [TestRepository::NAME => 'test'])]);

        $this->assertEquals(
            Collection::make([1 => TestEntity::make(1, 'test')]),
            $this->repository->getWhere([TestRepository::NAME => 1], 0, 1)
        );

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TestRepository::TABLE, [TestRepository::NAME => 1234], 0, 1])
            ->andReturn([]);

        $this->assertEquals(
            Collection::make([]),
            $this->repository->getWhere([TestRepository::NAME => 1234], 0, 1)
        );
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Repositories\AbstractRepository::all()
     */
    public function testAll()
    {
        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TestRepository::TABLE, [], 0, null])
            ->andReturn([new Record(1, [TestRepository::NAME => 'test'])]);

        $this->assertEquals(Collection::make([1 => TestEntity::make(1, 'test')]), $this->repository->all());

        $this->storage->shouldReceive('getWhere')
            ->once()
            ->withArgs([TestRepository::TABLE, [], 10, 10])
            ->andReturn([]);

        $this->assertEquals(Collection::make([]), $this->repository->all(10, 10));
    }
}

class TestRepository extends AbstractRepository
{
    public const TABLE = 'tests';
    public const NAME = 'name';

    /**
     * @param \BlixtTests\Persistence\Repositories\TestEntity|\Blixt\Persistence\Entities\Entity $entity
     *
     * @return array
     */
    public static function toAttributes(Entity $entity): array
    {
        return [
            static::NAME => $entity->getName()
        ];
    }

    /**
     * @param int $id
     * @param array $attributes
     *
     * @return \BlixtTests\Persistence\Repositories\TestEntity|\Blixt\Persistence\Entities\Entity
     */
    public static function toEntity(int $id, array $attributes): Entity
    {
        return TestEntity::make($id, $attributes[static::NAME]);
    }
}

class TestEntity extends Entity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * TestEntity constructor.
     *
     * @param int|null $id
     * @param string $name
     */
    public function __construct(?int $id, string $name)
    {
        parent::__construct($id);

        $this->setName($name);
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $id
     * @param string $name
     *
     * @return \BlixtTests\Persistence\Repositories\TestEntity
     */
    public static function make(int $id, string $name)
    {
        return new static($id, $name);
    }

    /**
     * @param string $name
     *
     * @return \BlixtTests\Persistence\Repositories\TestEntity
     */
    public static function create(string $name)
    {
        return new static(null, $name);
    }
}
