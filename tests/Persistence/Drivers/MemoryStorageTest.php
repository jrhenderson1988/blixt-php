<?php

namespace BlixtTests\Persistence\Drivers;

use Blixt\Exceptions\StorageException;
use Blixt\Persistence\Drivers\MemoryStorage;
use Blixt\Persistence\Record;
use Blixt\Persistence\Repositories\ColumnRepository;
use Blixt\Persistence\Repositories\SchemaRepository;
use BlixtTests\TestCase;

class MemoryStorageTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Drivers\MemoryStorage
     */
    protected $driver;

    public function setUp()
    {
        $this->driver = new MemoryStorage();
        $this->driver->install();
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(MemoryStorage::class, $this->driver);
    }

    /**
     * @test
     */
    public function testItDoesNotInitiallyExist()
    {
        $driver = new MemoryStorage();
        $this->assertFalse($driver->exists());
    }

    /**
     * @test
     */
    public function testCreateMethodCausesItToExist()
    {
        $driver = new MemoryStorage();
        $this->assertFalse($driver->exists());
        $this->assertTrue($driver->install());
        $this->assertTrue($driver->exists());
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testCreateReturnsRecordContainingNewIdWithProvidedAttributes()
    {
        $table = SchemaRepository::TABLE;
        $attributes = [SchemaRepository::NAME => 'foobar'];
        $record = $this->driver->create($table, $attributes);

        $this->assertInstanceOf(Record::class, $record);
        $this->assertEquals($attributes, $record->getAttributes());
        $this->assertNotNull($record->getId());
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testUpdateReturnsRecordContainingSameIdAndAttributes()
    {
        $table = SchemaRepository::TABLE;
        $attributes = [SchemaRepository::NAME => 'foobar'];
        $initial = $this->driver->create($table, $attributes);

        $this->assertInstanceOf(Record::class, $initial);
        $this->assertEquals($attributes, $initial->getAttributes());
        $this->assertNotNull($initial->getId());

        $newAttributes = [SchemaRepository::NAME => 'updated'];
        $updated = $this->driver->update($table, $initial->getId(), $newAttributes);

        $this->assertInstanceOf(Record::class, $updated);
        $this->assertEquals($newAttributes, $updated->getAttributes());
        $this->assertEquals($initial->getId(), $updated->getId());
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testUpdateThrowsStorageExceptionWhenRecordDoesNotAlreadyExist()
    {
        $table = SchemaRepository::TABLE;
        $id = 1234;
        $attributes = [SchemaRepository::NAME => 'foobar'];

        $this->expectException(StorageException::class);
        $this->driver->update($table, $id, $attributes);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testFindReturnsCorrectRecord()
    {
        $table = SchemaRepository::TABLE;

        $first = $this->driver->create($table, [SchemaRepository::NAME => 'first']);
        $second = $this->driver->create($table, [SchemaRepository::NAME => 'second']);
        $third = $this->driver->create($table, [SchemaRepository::NAME => 'third']);

        $this->assertEquals($first, $this->driver->find($table, $first->getId()));
        $this->assertEquals($second, $this->driver->find($table, $second->getId()));
        $this->assertEquals($third, $this->driver->find($table, $third->getId()));
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testFindReturnsNullWhenRecordDoesNotExist()
    {
        $table = SchemaRepository::TABLE;

        $this->driver->create($table, [SchemaRepository::NAME => 'first']);
        $this->driver->create($table, [SchemaRepository::NAME => 'second']);
        $this->driver->create($table, [SchemaRepository::NAME => 'third']);

        $this->assertNull($this->driver->find($table, 1234));
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testGetWhereReturnsArrayOfCorrespondingRecords()
    {
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'first']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'second']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'third']);

        $results = $this->driver->getWhere(SchemaRepository::TABLE, []);

        $this->assertTrue(is_array($results));
        $this->assertCount(3, $results);
        foreach ($results as $record) {
            $this->assertInstanceOf(Record::class, $record);
        }
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testGetWhereReturnsCorrectRecord()
    {
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'first']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'second']);
        $target = $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => $targetName = 'third']);

        $results = $this->driver->getWhere(SchemaRepository::TABLE, [SchemaRepository::NAME => $targetName]);
        $this->assertTrue(is_array($results));
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Record::class, $results[0]);
        $this->assertEquals($target, $results[0]);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testGetWhereReturnsCorrectRecordsWhenPassingArrayAsCondition()
    {
        $target1 = $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => $targetName1 = 'first']);
        $target2 = $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => $targetName2 = 'second']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'third']);

        $results = $this->driver->getWhere(SchemaRepository::TABLE, [
            SchemaRepository::NAME => [$targetName1, $targetName2]
        ]);

        $this->assertTrue(is_array($results));
        $this->assertCount(2, $results);
        foreach ($results as $record) {
            $this->assertInstanceOf(Record::class, $record);
            $this->assertTrue(in_array($record, [$target1, $target2]));
        }
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testGetWhereReturnsCorrectRecordWhenPassingMultipleConditions()
    {
        $this->driver->create(ColumnRepository::TABLE, [
            ColumnRepository::SCHEMA_ID => 1,
            ColumnRepository::NAME => 'first',
            ColumnRepository::IS_INDEXED => true,
            ColumnRepository::IS_STORED => false,
        ]);

        $this->driver->create(ColumnRepository::TABLE, [
            ColumnRepository::SCHEMA_ID => 2,
            ColumnRepository::NAME => 'second',
            ColumnRepository::IS_INDEXED => false,
            ColumnRepository::IS_STORED => true,
        ]);

        $target = $this->driver->create(ColumnRepository::TABLE, [
            ColumnRepository::SCHEMA_ID => 3,
            ColumnRepository::NAME => $targetName = 'target',
            ColumnRepository::IS_INDEXED => $targetIndexed = true,
            ColumnRepository::IS_STORED => true,
        ]);

        $results = $this->driver->getWhere(ColumnRepository::TABLE, [
            ColumnRepository::NAME => $targetName,
            ColumnRepository::IS_INDEXED => $targetIndexed
        ]);
        $this->assertTrue(is_array($results));
        $this->assertCount(1, $results);
        $this->assertInstanceOf(Record::class, $first = $results[0]);
        $this->assertEquals($target, $first);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testGetWhereReturnsEmptyArrayWhenNothingMatches()
    {
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'first']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'second']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'third']);

        $results = $this->driver->getWhere(SchemaRepository::TABLE, [SchemaRepository::NAME => 'fourth']);
        $this->assertTrue(is_array($results));
        $this->assertCount(0, $results);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testGetWhereCanBeLimited()
    {
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'first']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'second']);
        $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'third']);

        $notLimited = $this->driver->getWhere(SchemaRepository::TABLE, []);
        $this->assertTrue(is_array($notLimited));
        $this->assertCount(3, $notLimited);
        $limited = $this->driver->getWhere(SchemaRepository::TABLE, [], 0, 2);
        $this->assertTrue(is_array($limited));
        $this->assertCount(2, $limited);
    }

    /**
     * @test
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testGetWhereCanBeOffset()
    {
        $first = $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'first']);
        $second = $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'second']);
        $third = $this->driver->create(SchemaRepository::TABLE, [SchemaRepository::NAME => 'third']);

        $notOffset = $this->driver->getWhere(SchemaRepository::TABLE, []);
        $this->assertTrue(is_array($notOffset));
        $this->assertCount(3, $notOffset);
        $this->assertEquals($first, $notOffset[0]);
        $this->assertEquals($second, $notOffset[1]);
        $this->assertEquals($third, $notOffset[2]);

        $offset = $this->driver->getWhere(SchemaRepository::TABLE, [], 1);
        $this->assertTrue(is_array($offset));
        $this->assertCount(2, $offset);
        $this->assertEquals($second, $offset[0]);
        $this->assertEquals($third, $offset[1]);
    }
}
