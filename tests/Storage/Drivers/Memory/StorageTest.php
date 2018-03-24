<?php

namespace BlixtTests\Storage\Drivers\Memory;

use Blixt\Storage\Drivers\Memory\Repositories\ColumnRepository;
use Blixt\Storage\Drivers\Memory\Repositories\DocumentRepository;
use Blixt\Storage\Drivers\Memory\Repositories\FieldRepository;
use Blixt\Storage\Drivers\Memory\Repositories\OccurrenceRepository;
use Blixt\Storage\Drivers\Memory\Repositories\PositionRepository;
use Blixt\Storage\Drivers\Memory\Repositories\SchemaRepository;
use Blixt\Storage\Drivers\Memory\Repositories\TermRepository;
use Blixt\Storage\Drivers\Memory\Repositories\WordRepository;
use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Repositories\ColumnRepository as ColumnRepositoryInterface;
use Blixt\Storage\Repositories\DocumentRepository as DocumentRepositoryInterface;
use Blixt\Storage\Repositories\FieldRepository as FieldRepositoryInterface;
use Blixt\Storage\Repositories\OccurrenceRepository as OccurrenceRepositoryInterface;
use Blixt\Storage\Repositories\PositionRepository as PositionRepositoryInterface;
use Blixt\Storage\Repositories\SchemaRepository as SchemaRepositoryInterface;
use Blixt\Storage\Repositories\TermRepository as TermRepositoryInterface;
use Blixt\Storage\Repositories\WordRepository as WordRepositoryInterface;
use BlixtTests\TestCase;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;

class StorageTest extends TestCase
{
    /**
     * @var \Blixt\Storage\Drivers\Memory\Storage
     */
    protected $storage;

    public function setUp()
    {
        $this->storage = new Storage();
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(Storage::class, $this->storage);
    }

    /**
     * @test
     */
    public function testExistsReturnsFalseInitially()
    {
        $this->assertFalse($this->storage->exists());
    }

    /**
     * @test
     */
    public function testCreateCreatesTablesAndKeyReferencesAndExistsReturnsTrue()
    {
        $this->assertFalse($this->storage->exists());
        $this->storage->create();
        $this->assertTrue(is_array($tables = $this->getInaccessibleProperty($this->storage, 'tables')));
        $this->assertTrue(is_array($data = $this->getInaccessibleProperty($this->storage, 'data')));
        $this->assertTrue(is_array($keys = $this->getInaccessibleProperty($this->storage, 'keys')));
        foreach ($tables as $name) {
            $this->assertArrayHasKey($name, $data);
            $this->assertTrue(is_array($data[$name]));
            $this->assertArrayHasKey($name, $keys);
            $this->assertEquals(1, $keys[$name]);
        }
        $this->assertTrue($this->storage->exists());
    }

    /**
     * @test
     */
    public function testDestroyRemovesTablesAndKeyReferencesAndExistsReturnsFalse()
    {
        $this->storage->create();
        $data = $this->getInaccessibleProperty($this->storage, 'data');
        $keys = $this->getInaccessibleProperty($this->storage, 'keys');
        $this->assertNotEmpty($data);
        $this->assertNotEmpty($keys);
        $this->assertTrue($this->storage->exists());
        $this->storage->destroy();
        $data = $this->getInaccessibleProperty($this->storage, 'data');
        $keys = $this->getInaccessibleProperty($this->storage, 'keys');
        $this->assertEmpty($data);
        $this->assertEmpty($keys);
        $this->assertFalse($this->storage->exists());
    }

    /**
     * @test
     */
    public function testInsertThrowsExceptionWhenExistsIsFalse()
    {
        $this->assertFalse($this->storage->exists());
        $this->expectException(InvalidArgumentException::class);
        $this->storage->insert('schemas', ['name' => 'test']);
    }

    /**
     * @test
     */
    public function testInsertThrowsExceptionWithUnknownTableName()
    {
        $this->storage->create();
        $this->assertTrue($this->storage->exists());
        $this->expectException(InvalidArgumentException::class);
        $this->storage->insert('unknown-table-name', ['name' => 'test']);
    }

    /**
     * @test
     */
    public function testInsertCreatesRecordInCorrectTableWithUniqueAutoIncrementingKey()
    {
        $this->storage->create();
        $this->assertEquals(1, $this->getInaccessibleProperty($this->storage, 'keys')['schemas']);
        $this->assertEquals([], $this->getInaccessibleProperty($this->storage, 'data')['schemas']);
        $this->storage->insert('schemas', ['name' => 'test1']);
        $this->assertEquals([1 => ['name' => 'test1']], $this->getInaccessibleProperty($this->storage, 'data')['schemas']);
        $this->assertEquals(2, $this->getInaccessibleProperty($this->storage, 'keys')['schemas']);
        $this->storage->insert('schemas', ['name' => 'test2']);
        $this->assertEquals([1 => ['name' => 'test1'], 2 => ['name' => 'test2']], $this->getInaccessibleProperty($this->storage, 'data')['schemas']);
        $this->assertEquals(3, $this->getInaccessibleProperty($this->storage, 'keys')['schemas']);
        $this->assertEquals(1, $this->getInaccessibleProperty($this->storage, 'keys')['words']);
        $this->assertEquals([], $this->getInaccessibleProperty($this->storage, 'data')['words']);
        $this->storage->insert('words', ['word' => 'test1']);
        $this->assertEquals([1 => ['word' => 'test1']], $this->getInaccessibleProperty($this->storage, 'data')['words']);
        $this->assertEquals(2, $this->getInaccessibleProperty($this->storage, 'keys')['words']);
    }

    /**
     * @test
     */
    public function testUpdateThrowsExceptionWithUnknownTableName()
    {
        $this->storage->create();
        $this->assertTrue($this->storage->exists());
        $this->expectException(InvalidArgumentException::class);
        $this->storage->update('unknown-table-name', 1, ['name' => 'test']);
    }

    /**
     * @test
     */
    public function testUpdateChangesOnlyRecordSpecifiedByKey()
    {
        $this->storage->create();
        $this->storage->insert($table = 'schemas', $first = ['name' => 'test1']);
        $this->storage->insert($table, $second = ['name' => 'test2']);
        $this->storage->insert($table, $third = ['name' => 'test3']);
        $this->assertEquals([1 => $first, 2 => $second, 3 => $third], $this->getInaccessibleProperty($this->storage, 'data')[$table]);
        $this->storage->update($table, 2, $updatedSecond = ['name' => 'updated']);
        $this->assertEquals($updatedSecond, $this->getInaccessibleProperty($this->storage, 'data')[$table][2]);
        $this->assertEquals([1 => $first, 2 => $updatedSecond, 3 => $third], $this->getInaccessibleProperty($this->storage, 'data')[$table]);
    }

    /**
     * @test
     */
    public function testFindReturnsRecordIdentifiedByKey()
    {
        $this->storage->create();
        $this->storage->insert($table = 'schemas', $first = ['name' => 'test1']);
        $this->storage->insert($table, $second = ['name' => 'test2']);
        $this->storage->insert($table, $third = ['name' => 'test3']);
        $this->assertEquals($first, $this->storage->find($table, 1));
        $this->assertEquals($second, $this->storage->find($table, 2));
        $this->assertEquals($third, $this->storage->find($table, 3));
    }

    /**
     * @test
     */
    public function testGetWhereReturnsMultipleRecordsMatchingCondition()
    {
        $this->storage->create();
        $this->storage->insert($table = 'terms', $first = ['schema_id' => 1, 'word_id' => 1]);
        $this->storage->insert($table, $second = ['schema_id' => 1, 'word_id' => 2]);
        $this->storage->insert($table, $third = ['schema_id' => 2, 'word_id' => 1]);
        $this->assertTrue(in_array($first, $this->storage->getWhere($table, ['schema_id' => 1])));
        $this->assertTrue(in_array($second, $this->storage->getWhere($table, ['schema_id' => 1])));
        $this->assertTrue(in_array($first, $this->storage->getWhere($table, ['word_id' => 1])));
        $this->assertTrue(in_array($third, $this->storage->getWhere($table, ['word_id' => 1])));
        $this->assertTrue(in_array($third, $this->storage->getWhere($table, ['schema_id' => 2, 'word_id' => 1])));
    }

    /**
     * @test
     */
    public function testGetWherePreservesKeysInRecordSet()
    {
        $this->storage->create();
        $this->storage->insert($table = 'terms', $first = ['schema_id' => 1, 'word_id' => 1]);
        $this->storage->insert($table, $second = ['schema_id' => 1, 'word_id' => 2]);
        $this->storage->insert($table, $third = ['schema_id' => 2, 'word_id' => 1]);
        $this->assertEquals([1 => $first, 2 => $second], $this->storage->getWhere($table, ['schema_id' => 1]));
        $this->assertEquals([1 => $first, 3 => $third], $this->storage->getWhere($table, ['word_id' => 1]));
        $this->assertEquals([3 => $third], $this->storage->getWhere($table, ['schema_id' => 2, 'word_id' => 1]));
    }

    /**
     * @test
     */
    public function testGetWhereReturnsEmptyRecordSetWhenNoConditionsMatch()
    {
        $this->storage->create();
        $this->storage->insert($table = 'terms', $first = ['schema_id' => 1, 'word_id' => 1]);
        $this->storage->insert($table, $second = ['schema_id' => 1, 'word_id' => 2]);
        $this->storage->insert($table, $third = ['schema_id' => 2, 'word_id' => 1]);
        $this->assertTrue(is_array($this->storage->getWhere($table, ['schema_id' => 1, 'word_id' => 3])));
        $this->assertTrue(is_array($this->storage->getWhere($table, ['schema_id' => 3, 'word_id' => 1])));
        $this->assertEmpty($this->storage->getWhere($table, ['schema_id' => 1, 'word_id' => 3]));
        $this->assertEmpty($this->storage->getWhere($table, ['schema_id' => 3, 'word_id' => 1]));
    }

    /**
     * @test
     */
    public function testAllReturnsEntireRecordSet()
    {
        $this->storage->create();
        $this->storage->insert($table = 'terms', $first = ['schema_id' => 1, 'word_id' => 1]);
        $this->storage->insert($table, $second = ['schema_id' => 1, 'word_id' => 2]);
        $this->storage->insert($table, $third = ['schema_id' => 2, 'word_id' => 1]);
        $this->assertEquals([1 => $first, 2 => $second, 3 => $third], $this->storage->all($table));
    }

    /**
     * @test
     */
    public function testNextKeyReturnsAutoIncrementingIntegers()
    {
        $this->storage->create();
        $nextKey = new ReflectionMethod(Storage::class, 'nextKey');
        $nextKey->setAccessible(true);
        $this->assertEquals(1, $nextKey->invoke($this->storage, 'schemas'));
        $this->assertEquals(2, $nextKey->invoke($this->storage, 'schemas'));
        $this->assertEquals(3, $nextKey->invoke($this->storage, 'schemas'));
    }

    /**
     * @test
     */
    public function testNextKeyUsesSeparateKeyStoreForEachTable()
    {
        $this->storage->create();
        $nextKey = new ReflectionMethod(Storage::class, 'nextKey');
        $nextKey->setAccessible(true);
        $this->assertEquals(1, $nextKey->invoke($this->storage, 'schemas'));
        $this->assertEquals(2, $nextKey->invoke($this->storage, 'schemas'));
        $this->assertEquals(3, $nextKey->invoke($this->storage, 'schemas'));
        $this->assertEquals(1, $nextKey->invoke($this->storage, 'words'));
        $this->assertEquals(2, $nextKey->invoke($this->storage, 'words'));
        $this->assertEquals(1, $nextKey->invoke($this->storage, 'positions'));
        $this->assertEquals(2, $nextKey->invoke($this->storage, 'positions'));
        $this->assertEquals(3, $nextKey->invoke($this->storage, 'positions'));
        $this->assertEquals(4, $nextKey->invoke($this->storage, 'positions'));
        $this->assertEquals(4, $nextKey->invoke($this->storage, 'schemas'));
    }

    /**
     * @test
     */
    public function testColumnsReturnsColumnRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(ColumnRepositoryInterface::class, $this->storage->columns());
        $this->assertInstanceOf(ColumnRepository::class, $this->storage->columns());
    }

    /**
     * @test
     */
    public function testColumnsReturnsSameColumnRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->columns();
        $second = $this->storage->columns();
        $third = $this->storage->columns();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }

    /**
     * @test
     */
    public function testDocumentsReturnsDocumentRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(DocumentRepositoryInterface::class, $this->storage->documents());
        $this->assertInstanceOf(DocumentRepository::class, $this->storage->documents());
    }

    /**
     * @test
     */
    public function testDocumentsReturnsSameDocumentRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->documents();
        $second = $this->storage->documents();
        $third = $this->storage->documents();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }

    /**
     * @test
     */
    public function testFieldsReturnsFieldsRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(FieldRepositoryInterface::class, $this->storage->fields());
        $this->assertInstanceOf(FieldRepository::class, $this->storage->fields());
    }

    /**
     * @test
     */
    public function testFieldsReturnsSameFieldRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->fields();
        $second = $this->storage->fields();
        $third = $this->storage->fields();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }

    /**
     * @test
     */
    public function testOccurrencesReturnsOccurrencesRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(OccurrenceRepositoryInterface::class, $this->storage->occurrences());
        $this->assertInstanceOf(OccurrenceRepository::class, $this->storage->occurrences());
    }

    /**
     * @test
     */
    public function testOccurrencesReturnsSameOccurrenceRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->occurrences();
        $second = $this->storage->occurrences();
        $third = $this->storage->occurrences();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }

    /**
     * @test
     */
    public function testPositionsReturnsPositionsRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(PositionRepositoryInterface::class, $this->storage->positions());
        $this->assertInstanceOf(PositionRepository::class, $this->storage->positions());
    }

    /**
     * @test
     */
    public function testPositionsReturnsSamePositionRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->positions();
        $second = $this->storage->positions();
        $third = $this->storage->positions();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }

    /**
     * @test
     */
    public function testSchemasReturnsSchemasRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(SchemaRepositoryInterface::class, $this->storage->schemas());
        $this->assertInstanceOf(SchemaRepository::class, $this->storage->schemas());
    }

    /**
     * @test
     */
    public function testSchemasReturnsSameSchemaRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->schemas();
        $second = $this->storage->schemas();
        $third = $this->storage->schemas();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }

    /**
     * @test
     */
    public function testTermsReturnsTermsRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(TermRepositoryInterface::class, $this->storage->terms());
        $this->assertInstanceOf(TermRepository::class, $this->storage->terms());
    }

    /**
     * @test
     */
    public function testTermsReturnsSameTermRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->terms();
        $second = $this->storage->terms();
        $third = $this->storage->terms();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }

    /**
     * @test
     */
    public function testWordsReturnsWordsRepository()
    {
        $this->storage->create();
        $this->assertInstanceOf(WordRepositoryInterface::class, $this->storage->words());
        $this->assertInstanceOf(WordRepository::class, $this->storage->words());
    }

    /**
     * @test
     */
    public function testWordsReturnsSameWordRepositoryEachTime()
    {
        $this->storage->create();
        $first = $this->storage->words();
        $second = $this->storage->words();
        $third = $this->storage->words();
        $this->assertSame($first, $second);
        $this->assertSame($first, $third);
        $this->assertSame($second, $third);
    }
}