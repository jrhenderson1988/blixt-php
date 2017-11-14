<?php

namespace BlixtTests\Index;

use Blixt\Documents\Document as IndexableDocument;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Index\Index;
use Blixt\Index\Schema\Schema;
use Blixt\Models\Document;
use Blixt\Stemming\StemmerContract;
use Blixt\Storage\StorageEngineContract;
use Blixt\Storage\StorageFactoryContract;
use Blixt\Tokenization\TokenizerContract;
use BlixtTests\TestCase;
use Mockery;
use ReflectionClass;

class IndexTest extends TestCase
{
    /**
     * @var \Blixt\Storage\StorageFactoryContract
     */
    protected $factory;

    /**
     * @var \Blixt\Storage\StorageEngineContract
     */
    protected $engine;

    /**
     * @var \Blixt\Stemming\StemmerContract
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\TokenizerContract
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Index\Index
     */
    protected $index;

    public function setUp()
    {
        $this->factory = Mockery::mock(StorageFactoryContract::class);
        $this->stemmer = Mockery::mock(StemmerContract::class);
        $this->tokenizer = Mockery::mock(TokenizerContract::class);
        $this->engine = Mockery::mock(StorageEngineContract::class);

        $this->engine->expects('exists')->once()->andReturns(true);

        $this->index = new Index(
            $this->stemmer, $this->tokenizer, $this->engine
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructorThrowsIndexDoesNotExistExceptionWhenStorageDoesNotExistAndNoSchemaIsProvided()
    {
        $this->engine->expects('exists')->once()->andReturns(false);
        $this->engine->expects('getName')->once()->andReturns('test-index');
        $this->expectException(IndexDoesNotExistException::class);

        $this->index = new Index(
            $this->stemmer, $this->tokenizer, $this->engine, null
        );
    }

    public function testConstructorCallsStorageCreateInTransactionWhenStorageDoesNotExistAndSchemaIsProvided()
    {
        $this->engine->expects('exists')->once()->andReturns(false);
        $this->engine->expects('beginTransaction')->once()->andReturns(true);
        $this->engine->expects('create')->once()->andReturns(true);
        $this->engine->expects('commitTransaction')->once()->andReturns(true);

        $this->index = new Index(
            $this->stemmer, $this->tokenizer, $this->engine, new Schema()
        );
    }

    public function testConstructorAcceptsStorageEngineStemmerAndTokenizer()
    {
        $reflection = new ReflectionClass(Index::class);

        $storage = $reflection->getProperty('storage');
        $stemmer = $reflection->getProperty('stemmer');
        $tokenizer = $reflection->getProperty('tokenizer');

        $storage->setAccessible(true);
        $stemmer->setAccessible(true);
        $tokenizer->setAccessible(true);

        $this->assertEquals($this->engine, $storage->getValue($this->index));
        $this->assertEquals($this->stemmer, $stemmer->getValue($this->index));
        $this->assertEquals($this->tokenizer, $tokenizer->getValue($this->index));
    }

    public function testDestroyMethodCallsStorageDestroyWhenStorageExists()
    {
        $this->engine->shouldReceive('exists')->andReturn(true);
        $this->engine->shouldReceive('destroy')->andReturn(true);

        $this->assertTrue($this->index->destroy());
    }

    public function testDestroyMethodDoesNotCallStorageDestroyWhenStorageDoesNotExist()
    {
        $this->engine->shouldReceive('exists')->andReturn(false);
        $this->engine->shouldNotReceive('destroy');

        $this->assertFalse($this->index->destroy());
    }
}