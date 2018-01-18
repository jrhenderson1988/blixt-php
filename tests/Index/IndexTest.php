<?php

namespace BlixtTests\Index;

use Blixt\Documents\Document as IndexableDocument;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\UndefinedSchemaException;
use Blixt\Index\Index;
use Blixt\Index\Schema\Blueprint;
use Blixt\Models\Document;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;
use BlixtTests\TestCase;
use Mockery;
use ReflectionClass;

class IndexTest extends TestCase
{
    /**
     * @var \Blixt\Storage\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Index\Index
     */
    protected $index;

    public function setUp()
    {
//        $this->stemmer = Mockery::mock(StemmerContract::class);
//        $this->tokenizer = Mockery::mock(TokenizerContract::class);
//        $this->storage = Mockery::mock(StorageContract::class);
//
//        $this->storage->shouldReceive('exists')->once()->andReturn(true);
//        $this->index = new Index($this->stemmer, $this->tokenizer, $this->storage);
    }

    public function tearDown()
    {
//        Mockery::close();
    }

    public function testConstructorSkipsCreationForExistingIndexes()
    {
//        $stemmer = Mockery::mock(StemmerContract::class);
//        $tokenizer = Mockery::mock(TokenizerContract::class);
//        $storage = Mockery::mock(StorageContract::class);
//
//        $storage->shouldReceive('exists')->once()->andReturnTrue();
//        new Index($stemmer, $tokenizer, $storage);
    }

    public function testConstructorCreatesNonExistingIndexes()
    {
//        $stemmer = Mockery::mock(StemmerContract::class);
//        $tokenizer = Mockery::mock(TokenizerContract::class);
//        $storage = Mockery::mock(StorageContract::class);
//
//        $schema = new Schema();
//        $storage->shouldReceive('exists')->once()->andReturnFalse();
//        $storage->shouldReceive('beginTransaction')->andReturnTrue();
//        $storage->shouldReceive('create')->withArgs([$schema])->andReturnTrue();
//        $storage->shouldReceive('commitTransaction')->andReturnTrue();
//
//        new Index($stemmer, $tokenizer, $storage, $schema);
    }

    public function testConstructorThrowsUndefinedSchemaExceptionWhenNoSchemaProvidedAndIndexDoesNotExist()
    {
//        $stemmer = Mockery::mock(StemmerContract::class);
//        $tokenizer = Mockery::mock(TokenizerContract::class);
//        $storage = Mockery::mock(StorageContract::class);
//
//        $storage->shouldReceive('exists')->once()->andReturnFalse();
//        $storage->shouldReceive('getName')->once()->andReturn('TEST');
//        $this->expectException(UndefinedSchemaException::class);
//        $this->expectExceptionMessage('TEST');
//
//        new Index($stemmer, $tokenizer, $storage);
    }

    public function testConstructorCreatesStorageEngineStemmerAndTokenizer()
    {
//        $reflection = new ReflectionClass(Index::class);
//
//        $storageProperty = $reflection->getProperty('storage');
//        $stemmerProperty = $reflection->getProperty('stemmer');
//        $tokenizerProperty = $reflection->getProperty('tokenizer');
//
//        $storageProperty->setAccessible(true);
//        $stemmerProperty->setAccessible(true);
//        $tokenizerProperty->setAccessible(true);
//
//        $stemmer = Mockery::mock(StemmerContract::class);
//        $tokenizer = Mockery::mock(TokenizerContract::class);
//        $storage = Mockery::mock(StorageContract::class);
//        $storage->shouldReceive('exists')->once()->andReturnTrue();
//
//        $index = new Index($stemmer, $tokenizer, $storage);
//
//        $this->assertEquals($storage, $storageProperty->getValue($index));
//        $this->assertEquals($stemmer, $stemmerProperty->getValue($index));
//        $this->assertEquals($tokenizer, $tokenizerProperty->getValue($index));
    }

    public function testTransactionIsRolledBackWhenAnExceptionIsThrownDuringCreate()
    {
//        $schema = Mockery::mock(Schema::class);
//
//        $exceptionMessage = str_random(20);
//        $this->storage->shouldReceive('beginTransaction')->andReturn(true);
//        $this->storage->shouldReceive('create')->withArgs([$schema])->andThrow(\PDOException::class, $exceptionMessage);
//        $this->storage->shouldReceive('rollBackTransaction')->andReturn(true);
//
//        $this->expectException(\PDOException::class);
//        $this->expectExceptionMessage($exceptionMessage);
//        $this->index->create($schema);
    }

    public function testDestroyMethodCallsStorageDestroyWhenStorageExists()
    {
//        $this->storage->shouldReceive('exists')->andReturn(true);
//        $this->storage->shouldReceive('destroy')->andReturn(true);
//
//        $this->assertTrue($this->index->destroy());
    }

    public function testDestroyMethodDoesNotCallStorageDestroyWhenStorageDoesNotExist()
    {
//        $this->storage->shouldReceive('exists')->andReturn(false);
//        $this->storage->shouldNotReceive('destroy');
//
//        $this->assertFalse($this->index->destroy());
    }

    public function testAddMethodThrowsDocumentAlreadyExistsExceptionWhenDocumentAlreadyExists()
    {
//        $this->storage->shouldReceive('findDocumentByKey')->withArgs([1])->andReturn(new Document(1, 1));
//        $this->expectException(DocumentAlreadyExistsException::class);
//
//        $document = new IndexableDocument(1);
//        $this->index->add($document);
    }
}