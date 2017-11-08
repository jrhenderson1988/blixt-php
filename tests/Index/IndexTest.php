<?php

namespace BlixtTests\Index;

use Blixt\Documents\Document as IndexableDocument;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Index\Index;
use Blixt\Index\Schema\Schema;
use Blixt\Models\Document;
use Blixt\Stemming\StemmerInterface;
use Blixt\Storage\EngineInterface;
use Blixt\Storage\FactoryInterface;
use Blixt\Tokenization\TokenizerInterface;
use BlixtTests\TestCase;
use Mockery;
use ReflectionClass;

class IndexTest extends TestCase
{
    /**
     * @var \Blixt\Storage\FactoryInterface
     */
    protected $factory;

    /**
     * @var \Blixt\Storage\EngineInterface
     */
    protected $engine;

    /**
     * @var \Blixt\Stemming\StemmerInterface
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\TokenizerInterface
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Index\Index
     */
    protected $index;

    public function setUp()
    {
        $this->factory = Mockery::mock(FactoryInterface::class);
        $this->stemmer = Mockery::mock(StemmerInterface::class);
        $this->tokenizer = Mockery::mock(TokenizerInterface::class);
        $this->engine = Mockery::mock(EngineInterface::class);

        $this->factory->expects()->create('test')->once()->andReturns($this->engine);

        $this->index = new Index('test', $this->factory, $this->stemmer, $this->tokenizer);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructorCreatesStorageEngineStemmerAndTokenizer()
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

    public function testExistsCallsStorageExists()
    {
        $this->engine->shouldReceive('exists')->andReturn(true, false);
        $this->assertTrue($this->index->exists());
        $this->assertFalse($this->index->exists());
    }

    public function testCreateMethodCallsStorageCreateAndUsesTransactions()
    {
        $schema = Mockery::mock(Schema::class);

        $this->engine->shouldReceive('beginTransaction')->andReturn(true);
        $this->engine->shouldReceive('create')->withArgs([$schema])->andReturn(true);
        $this->engine->shouldReceive('commitTransaction')->andReturn(true);

        $this->assertEquals(true, $this->index->create($schema));
    }

    public function testTransactionIsRolledBackWhenAnExceptionIsThrownDuringCreate()
    {
        $schema = Mockery::mock(Schema::class);

        $exceptionMessage = str_random(20);
        $this->engine->shouldReceive('beginTransaction')->andReturn(true);
        $this->engine->shouldReceive('create')->withArgs([$schema])->andThrow(\PDOException::class, $exceptionMessage);
        $this->engine->shouldReceive('rollBackTransaction')->andReturn(true);

        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->index->create($schema);
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

    public function testAddMethodThrowsDocumentAlreadyExistsExceptionWhenDocumentAlreadyExists()
    {
        $this->engine->shouldReceive('findDocumentByKey')->withArgs([1])->andReturn(new Document(1, 1));
        $this->expectException(DocumentAlreadyExistsException::class);

        $document = new IndexableDocument(1);
        $this->index->add($document);
    }
}