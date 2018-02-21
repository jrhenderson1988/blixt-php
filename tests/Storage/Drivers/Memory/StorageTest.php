<?php

namespace BlixtTests\Storage\Drivers\Memory;

use Blixt\Exceptions\StorageException;
use Blixt\Storage\Drivers\Memory\Storage;
use BlixtTests\TestCase;
use InvalidArgumentException;

class StorageTest extends TestCase
{
    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    /** @test */
    public function setUp()
    {
        $this->storage = new Storage();
    }

    /** @test */
    public function testExistsReturnsFalseInitially()
    {
        $this->assertFalse($this->storage->exists());
    }

    /** @test */
    public function testCreateCreatesTablesAndKeyReferencesAndExistsReturnsTrue()
    {
        $this->assertFalse($this->storage->exists());

        $this->storage->create();

        $reflection = new \ReflectionClass($this->storage);
        $tables = $reflection->getProperty('tables');
        $data = $reflection->getProperty('data');
        $keys = $reflection->getProperty('keys');
        $tables->setAccessible(true);
        $data->setAccessible(true);
        $keys->setAccessible(true);

        $this->assertTrue(is_array($tables->getValue($this->storage)));
        $this->assertTrue(is_array($data->getValue($this->storage)));
        $this->assertTrue(is_array($keys->getValue($this->storage)));

        foreach ($tables->getValue($this->storage) as $name) {
            $this->assertArrayHasKey($name, $data->getValue($this->storage));
            $this->assertTrue(is_array($data->getValue($this->storage)[$name]));

            $this->assertArrayHasKey($name, $keys->getValue($this->storage));
            $this->assertEquals(1, $keys->getValue($this->storage)[$name]);
        }

        $this->assertTrue($this->storage->exists());
    }

    /** @test */
    public function testDestroyRemovesTablesAndKeyReferencesAndExistsReturnsFalse()
    {
        $this->storage->create();

        $reflection = new \ReflectionClass($this->storage);
        $data = $reflection->getProperty('data');
        $keys = $reflection->getProperty('keys');
        $data->setAccessible(true);
        $keys->setAccessible(true);

        $this->assertNotEmpty($data->getValue($this->storage));
        $this->assertNotEmpty($keys->getValue($this->storage));
        $this->assertTrue($this->storage->exists());

        $this->storage->destroy();

        $this->assertEmpty($data->getValue($this->storage));
        $this->assertEmpty($keys->getValue($this->storage));
        $this->assertFalse($this->storage->exists());
    }

    /** @test */
    public function testInsertThrowsExceptionWhenExistsIsFalse()
    {
        $this->assertFalse($this->storage->exists());
        $this->expectException(InvalidArgumentException::class);
        $this->storage->insert('schemas', ['name' => 'test']);
    }

    /** @test  */
    public function testInsertThrowsExceptionWithUnknownTableName()
    {
        $this->storage->create();
        $this->assertTrue($this->storage->exists());

        $this->expectException(InvalidArgumentException::class);
        $this->storage->insert('unknown-table-name', ['name' => 'test']);
    }

    /** @test */
    public function testInsertCreatesRecordInCorrectTableWithUniqueAutoincrementingKey()
    {
        $reflection = new \ReflectionClass($this->storage);
        $data = $reflection->getProperty('data');
        $keys = $reflection->getProperty('keys');
        $data->setAccessible(true);
        $keys->setAccessible(true);

        $this->storage->create();

        $this->assertEquals(1, $keys->getValue($this->storage)['schemas']);
        $this->assertEquals([], $data->getValue($this->storage)['schemas']);

        $this->storage->insert('schemas', ['name' => 'test1']);
        $this->assertEquals([1 => ['name' => 'test1']], $data->getValue($this->storage)['schemas']);
        $this->assertEquals(2, $keys->getValue($this->storage)['schemas']);

        $this->storage->insert('schemas', ['name' => 'test2']);
        $this->assertEquals([1 => ['name' => 'test1'], 2 => ['name' => 'test2']], $data->getValue($this->storage)['schemas']);
        $this->assertEquals(3, $keys->getValue($this->storage)['schemas']);

        $this->assertEquals(1, $keys->getValue($this->storage)['words']);
        $this->assertEquals([], $data->getValue($this->storage)['words']);

        $this->storage->insert('words', ['word' => 'test1']);
        $this->assertEquals([1 => ['word' => 'test1']], $data->getValue($this->storage)['words']);
        $this->assertEquals(2, $keys->getValue($this->storage)['words']);
    }

    /** @test */
    public function testUpdateThrowsExceptionWithUnknownTableName()
    {
        $this->storage->create();
        $this->assertTrue($this->storage->exists());

        $this->expectException(InvalidArgumentException::class);
        $this->storage->update('unknown-table-name', 1, ['name' => 'test']);
    }

    public function testUpdateChangesOnlyRecordSpecifiedByKey()
    {
        $table = 'schemas';
        $first = ['name' => 'test1'];
        $second = ['name' => 'test2'];
        $third = ['name' => 'test3'];

        $this->storage->create();
        $this->storage->insert($table, $first);
        $this->storage->insert($table, $second);
        $this->storage->insert($table, $third);

        $reflection = new \ReflectionClass($this->storage);
        $data = $reflection->getProperty('data');
        $keys = $reflection->getProperty('keys');
        $data->setAccessible(true);
        $keys->setAccessible(true);

        $this->assertEquals([1 => $first, 2 => $second, 3 => $third], $data->getValue($this->storage)[$table]);

        $updatedSecond = ['name' => 'updated'];
        $this->storage->update($table, 2, $updatedSecond);

        $this->assertEquals($updatedSecond, $data->getValue($this->storage)[$table][2]);
        $this->assertEquals([1 => $first, 2 => $updatedSecond, 3 => $third], $data->getValue($this->storage)[$table]);
    }

    public function testFindReturnsRecordIdentifiedByKey() {}
    public function testGetWhereReturnsMultipleRecordsMatchingCondition() {}
    public function testGetWherePreservesKeysInRecordSet() {}
    public function testGetWhereReturnsEmptyRecordSetWhenNoConditionsMatch() {}
    public function testAllReturnsEntireRecordSet() {}
    public function testNextKeyReturnsAutoincrementingIntegers() {} // protected
    public function testNextKeyUsesSeparateKeyStoreForEachTable() {} // protected
    public function testColumnsReturnsColumnRepository() {}
    public function testColumnsReturnsSameColumnRepositoryEachTime() {}
    public function testDocumentsReturnsDocumentRepository() {}
    public function testFieldsReturnsFieldsRepository() {}
    public function testOccurrencesReturnsOccurrencesRepository() {}
    public function testPositionsReturnsPositionsRepository() {}
    public function testSchemasReturnsSchemasRepository() {}
    public function testTermsReturnsTermsRepository() {}
    public function testWordsReturnsWordsRepository() {}
}