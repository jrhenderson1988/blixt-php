<?php

namespace BlixtTests\Persistence\Drivers;

use Blixt\Exceptions\StorageException;
use Blixt\Persistence\Drivers\MemoryDriver;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Column;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class MemoryDriverTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Drivers\MemoryDriver
     */
    protected $driver;

    public function setUp()
    {
        $this->driver = new MemoryDriver();
        $this->driver->create();
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(MemoryDriver::class, $this->driver);
    }

    /**
     * @test
     */
    public function testItDoesNotInitiallyExist()
    {
        $driver = new MemoryDriver();
        $this->assertFalse($driver->exists());
    }

    /**
     * @test
     */
    public function testCreateMethodCausesItToExist()
    {
        $driver = new MemoryDriver();
        $this->assertFalse($driver->exists());
        $this->assertTrue($driver->create());
        $this->assertTrue($driver->exists());
    }

    /**
     * @test
     */
    public function testInsertReturnsEquivalentEntityWithIDSet()
    {
        $entity = Schema::create('name');
        $this->assertNull($entity->getId());
        $created = $this->driver->insert($entity);
        $this->assertEquals(get_class($created), get_class($entity));
        $this->assertNotNull($created->getId());
        $entity->setId($created->getId());
        $this->assertEquals($entity, $created);
    }

    /**
     * @test
     */
    public function testUpdateReturnsEquivalentEntity()
    {
        $initial = Schema::create('test');
        $entity = $this->driver->insert($initial);
        $this->assertNotNull($initial->getId());
        $changed = clone $entity;
        $entity->setName('testing');
        $updated = $this->driver->update($changed);
        $this->assertEquals($changed, $updated);
    }

    /**
     * @test
     */
    public function testUpdateThrowsStorageExceptionWhenEntityDoesNotAlreadyExist()
    {
        $entity = Schema::make(1, 'test');
        $this->expectException(StorageException::class);
        $this->driver->update($entity);
    }

    /**
     * @test
     */
    public function testFindReturnsCorrectEntity()
    {
        $first = $this->driver->insert(Schema::create('first'));
        $second = $this->driver->insert(Schema::create('second'));
        $third = $this->driver->insert(Schema::create('third'));

        $this->assertEquals($first, $firstFound = $this->driver->find(Schema::class, $first->getId()));
        $this->assertEquals($second, $secondFound = $this->driver->find(Schema::class, $second->getId()));
        $this->assertEquals($third, $thirdFound = $this->driver->find(Schema::class, $third->getId()));

        $this->assertInstanceOf(Schema::class, $firstFound);
        $this->assertInstanceOf(Schema::class, $secondFound);
        $this->assertInstanceOf(Schema::class, $thirdFound);
    }

    /**
     * @test
     */
    public function testFindReturnsNullWhenEntityDoesNotExist()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $this->assertNull($this->driver->find(Schema::class, 10));
    }

    /**
     * @test
     */
    public function testFindByReturnsCorrectEntity()
    {
        $this->driver->insert(Schema::create('first'));
        $target = $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $this->assertEquals($target, $this->driver->findBy(Schema::class, [Schema::FIELD_NAME => 'second']));
    }

    /**
     * @test
     */
    public function testFindByReturnsCorrectEntityWhenPassingCollectionAsCondition()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $target = $this->driver->insert(Schema::create('third'));

        $this->assertEquals($target, $this->driver->findBy(Schema::class, [
            Schema::FIELD_NAME => Collection::make(['third', 'fourth', 'fifth'])
        ]));
    }

    /**
     * @test
     */
    public function testFindByReturnsCorrectEntityWhenPassingArrayAsCondition()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $target = $this->driver->insert(Schema::create('third'));

        $this->assertEquals($target, $this->driver->findBy(Schema::class, [
            Schema::FIELD_NAME => ['third', 'fourth', 'fifth']
        ]));
    }

    /**
     * @test
     */
    public function testFindByReturnsCorrectEntityWhenPassingMultipleConditions()
    {
        $this->driver->insert(Column::create(1, 'first', true, false));
        $target = $this->driver->insert(Column::create(1, 'second', true, false));
        $this->driver->insert(Column::create(1, 'third', true, false));

        $found = $this->driver->findBy(Column::class, [
            Column::FIELD_NAME => 'second',
            Column::FIELD_IS_INDEXED => true
        ]);
        $this->assertInstanceOf(Column::class, $found);
        $this->assertEquals($target, $found);
    }

    /**
     * @test
     */
    public function testFindByReturnsNullIfNothingMatches()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $this->assertNull($this->driver->findBy(Schema::class, [Schema::FIELD_NAME => 'fourth']));
    }

    /**
     * @test
     */
    public function testGetWhereReturnsCollectionOfCorrespondingEntities()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $collection = $this->driver->getWhere(Schema::class, []);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(3, $collection->count());
        $collection->each(function ($item) {
            $this->assertInstanceOf(Schema::class, $item);
        });
    }

    /**
     * @test
     */
    public function testGetWhereReturnsCorrectValues()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $collection = $this->driver->getWhere(Schema::class, [Schema::FIELD_NAME => 'third']);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(1, $collection->count());
        $item = $collection->first();
        $this->assertInstanceOf(Schema::class, $item);
        $this->assertEquals('third', $item->getName());
    }

    /**
     * @test
     */
    public function testGetWhereReturnsCorrectEntitiesWhenPassingCollectionAsCondition()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $collection = $this->driver->getWhere(Schema::class, [
            Schema::FIELD_NAME => Collection::make(['first', 'second'])
        ]);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(2, $collection->count());
        $collection->each(function ($item) {
            $this->assertInstanceOf(Schema::class, $item);
            $this->assertTrue(in_array($item->getName(), ['first', 'second']));
        });
    }

    /**
     * @test
     */
    public function testGetWhereReturnsCorrectEntitiesWhenPassingArrayAsCondition()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $collection = $this->driver->getWhere(Schema::class, [
            Schema::FIELD_NAME => ['first', 'second']
        ]);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(2, $collection->count());
        $collection->each(function ($item) {
            $this->assertInstanceOf(Schema::class, $item);
            $this->assertTrue(in_array($item->getName(), ['first', 'second']));
        });
    }

    /**
     * @test
     */
    public function testGetWhereReturnsCorrectEntityWhenPassingMultipleConditions()
    {
        $this->driver->insert(Column::create(1, 'first', true, false));
        $target = $this->driver->insert(Column::create(1, 'second', true, false));
        $this->driver->insert(Column::create(1, 'third', true, false));

        $collection = $this->driver->getWhere(Column::class, [
            Column::FIELD_NAME => 'second',
            Column::FIELD_IS_INDEXED => true
        ]);
        $this->assertInstanceOf(Collection::class, $collection);
        $first = $collection->first();
        $this->assertInstanceOf(Column::class, $first);
        $this->assertEquals($target, $first);
    }

    /**
     * @test
     */
    public function testGetWhereReturnsEmptyCollectionWhenNothingMatches()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $collection = $this->driver->getWhere(Schema::class, [Schema::FIELD_NAME => 'fourth']);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     */
    public function testGetWhereCanBeLimited()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $notLimited = $this->driver->getWhere(Schema::class, []);
        $this->assertInstanceOf(Collection::class, $notLimited);
        $this->assertEquals(3, $notLimited->count());
        $limited = $this->driver->getWhere(Schema::class, [], 0, 2);
        $this->assertInstanceOf(Collection::class, $limited);
        $this->assertEquals(2, $limited->count());
    }

    /**
     * @test
     */
    public function testGetWhereCanBeOffset()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $notOffset = $this->driver->getWhere(Schema::class, []);
        $this->assertInstanceOf(Collection::class, $notOffset);
        $this->assertEquals(3, $notOffset->count());
        $this->assertEquals('first', $notOffset->first()->getName());
        $offset = $this->driver->getWhere(Schema::class, [], 1);
        $this->assertInstanceOf(Collection::class, $offset);
        $this->assertEquals(2, $offset->count());
        $this->assertEquals('second', $offset->first()->getName());
    }

    /**
     * @test
     */
    public function testAllReturnsCollectionContainingAllEntities()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $collection = $this->driver->all(Schema::class);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(3, $collection->count());
        $collection->each(function ($item) {
            $this->assertInstanceOf(Schema::class, $item);
            $this->assertTrue(in_array($item->getName(), ['first', 'second', 'third']));
        });
    }

    /**
     * @test
     */
    public function testAllReturnsEmptyCollectionWhenThereAreNoRecords()
    {
        $schemas = $this->driver->all(Schema::class);
        $this->assertInstanceOf(Collection::class, $schemas);
        $this->assertEquals(0, $schemas->count());
    }

    /**
     * @test
     */
    public function testAllCanBeLimited()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $notLimited = $this->driver->all(Schema::class);
        $this->assertInstanceOf(Collection::class, $notLimited);
        $this->assertEquals(3, $notLimited->count());
        $limited = $this->driver->all(Schema::class, 0, 2);
        $this->assertInstanceOf(Collection::class, $limited);
        $this->assertEquals(2, $limited->count());
    }

    /**
     * @test
     */
    public function testAllCanBeOffset()
    {
        $this->driver->insert(Schema::create('first'));
        $this->driver->insert(Schema::create('second'));
        $this->driver->insert(Schema::create('third'));

        $notOffset = $this->driver->all(Schema::class);
        $this->assertInstanceOf(Collection::class, $notOffset);
        $this->assertEquals(3, $notOffset->count());
        $this->assertEquals('first', $notOffset->first()->getName());
        $offset = $this->driver->all(Schema::class, 1);
        $this->assertInstanceOf(Collection::class, $offset);
        $this->assertEquals(2, $offset->count());
        $this->assertEquals('second', $offset->first()->getName());
    }
}