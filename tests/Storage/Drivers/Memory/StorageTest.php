<?php

namespace BlixtTests\Storage\Drivers\Memory;

use Blixt\Exceptions\StorageException;
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

        $reflection = new ReflectionClass($this->storage);
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

        $reflection = new ReflectionClass($this->storage);
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
        $reflection = new ReflectionClass($this->storage);
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

        $reflection = new ReflectionClass($this->storage);
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

    public function testFindReturnsRecordIdentifiedByKey()
    {
        $table = 'schemas';
        $first = ['name' => 'test1'];
        $second = ['name' => 'test2'];
        $third = ['name' => 'test3'];

        $this->storage->create();
        $this->storage->insert($table, $first);
        $this->storage->insert($table, $second);
        $this->storage->insert($table, $third);

        $this->assertEquals($first, $this->storage->find($table, 1));
        $this->assertEquals($second, $this->storage->find($table, 2));
        $this->assertEquals($third, $this->storage->find($table, 3));
    }

    public function testGetWhereReturnsMultipleRecordsMatchingCondition()
    {
        $table = 'terms';
        $first = ['schema_id' => 1, 'word_id' => 1];
        $second = ['schema_id' => 1, 'word_id' => 2];
        $third = ['schema_id' => 2, 'word_id' => 1];

        $this->storage->create();
        $this->storage->insert($table, $first);
        $this->storage->insert($table, $second);
        $this->storage->insert($table, $third);

        $this->assertTrue(in_array($first, $this->storage->getWhere($table, ['schema_id' => 1])));
        $this->assertTrue(in_array($second, $this->storage->getWhere($table, ['schema_id' => 1])));
        $this->assertTrue(in_array($first, $this->storage->getWhere($table, ['word_id' => 1])));
        $this->assertTrue(in_array($third, $this->storage->getWhere($table, ['word_id' => 1])));
        $this->assertTrue(in_array($third, $this->storage->getWhere($table, ['schema_id' => 2, 'word_id' => 1])));
    }

    public function testGetWherePreservesKeysInRecordSet()
    {
        $table = 'terms';
        $first = ['schema_id' => 1, 'word_id' => 1];
        $second = ['schema_id' => 1, 'word_id' => 2];
        $third = ['schema_id' => 2, 'word_id' => 1];

        $this->storage->create();
        $this->storage->insert($table, $first);
        $this->storage->insert($table, $second);
        $this->storage->insert($table, $third);

        $this->assertEquals([1 => $first, 2 => $second], $this->storage->getWhere($table, ['schema_id' => 1]));
        $this->assertEquals([1 => $first, 3 => $third], $this->storage->getWhere($table, ['word_id' => 1]));
        $this->assertEquals([3 => $third], $this->storage->getWhere($table, ['schema_id' => 2, 'word_id' => 1]));
    }

    public function testGetWhereReturnsEmptyRecordSetWhenNoConditionsMatch()
    {
        $table = 'terms';
        $first = ['schema_id' => 1, 'word_id' => 1];
        $second = ['schema_id' => 1, 'word_id' => 2];
        $third = ['schema_id' => 2, 'word_id' => 1];

        $this->storage->create();
        $this->storage->insert($table, $first);
        $this->storage->insert($table, $second);
        $this->storage->insert($table, $third);

        $this->assertTrue(is_array($this->storage->getWhere($table, ['schema_id' => 1, 'word_id' => 3])));
        $this->assertTrue(is_array($this->storage->getWhere($table, ['schema_id' => 3, 'word_id' => 1])));
        $this->assertEmpty($this->storage->getWhere($table, ['schema_id' => 1, 'word_id' => 3]));
        $this->assertEmpty($this->storage->getWhere($table, ['schema_id' => 3, 'word_id' => 1]));
    }

    public function testAllReturnsEntireRecordSet()
    {
        $table = 'terms';
        $first = ['schema_id' => 1, 'word_id' => 1];
        $second = ['schema_id' => 1, 'word_id' => 2];
        $third = ['schema_id' => 2, 'word_id' => 1];

        $this->storage->create();
        $this->storage->insert($table, $first);
        $this->storage->insert($table, $second);
        $this->storage->insert($table, $third);

        $this->assertEquals([1 => $first, 2 => $second, 3 => $third], $this->storage->all($table));
    }

    public function testNextKeyReturnsAutoincrementingIntegers()
    {
        $this->storage->create();

        $nextKey = new ReflectionMethod(Storage::class, 'nextKey');
        $nextKey->setAccessible(true);

        $this->assertEquals(1, $nextKey->invoke($this->storage, 'schemas'));
        $this->assertEquals(2, $nextKey->invoke($this->storage, 'schemas'));
        $this->assertEquals(3, $nextKey->invoke($this->storage, 'schemas'));
    }

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

    public function testColumnsReturnsColumnRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(ColumnRepositoryInterface::class, $this->storage->columns());
        $this->assertInstanceOf(ColumnRepository::class, $this->storage->columns());
    }

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

    public function testDocumentsReturnsDocumentRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(DocumentRepositoryInterface::class, $this->storage->documents());
        $this->assertInstanceOf(DocumentRepository::class, $this->storage->documents());
    }

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

    public function testFieldsReturnsFieldsRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(FieldRepositoryInterface::class, $this->storage->fields());
        $this->assertInstanceOf(FieldRepository::class, $this->storage->fields());
    }

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

    public function testOccurrencesReturnsOccurrencesRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(OccurrenceRepositoryInterface::class, $this->storage->occurrences());
        $this->assertInstanceOf(OccurrenceRepository::class, $this->storage->occurrences());
    }

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

    public function testPositionsReturnsPositionsRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(PositionRepositoryInterface::class, $this->storage->positions());
        $this->assertInstanceOf(PositionRepository::class, $this->storage->positions());
    }

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

    public function testSchemasReturnsSchemasRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(SchemaRepositoryInterface::class, $this->storage->schemas());
        $this->assertInstanceOf(SchemaRepository::class, $this->storage->schemas());
    }

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

    public function testTermsReturnsTermsRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(TermRepositoryInterface::class, $this->storage->terms());
        $this->assertInstanceOf(TermRepository::class, $this->storage->terms());
    }

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

    public function testWordsReturnsWordsRepository()
    {
        $this->storage->create();

        $this->assertInstanceOf(WordRepositoryInterface::class, $this->storage->words());
        $this->assertInstanceOf(WordRepository::class, $this->storage->words());
    }

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