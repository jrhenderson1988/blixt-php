<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Entities\Word;
use BlixtTests\TestCase;

class TermTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Term
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Term(1, 2, 3, 4);
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertNotNull($this->entity);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::getSchemaId()
     */
    public function testGetSchemaIdId()
    {
        $this->assertEquals(2, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::setSchemaId()
     */
    public function testSetSchemaId()
    {
        $this->entity->setSchemaId(3);
        $this->assertEquals(3, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::setSchemaIdFromSchema()
     */
    public function testSetSchemaIdFromSchema()
    {
        $this->entity->setSchemaIdFromSchema(new Schema(3, 'foobar'));
        $this->assertEquals(3, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::getWordId()
     */
    public function testGetWordId()
    {
        $this->assertEquals(3, $this->entity->getWordId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::setWordId()
     */
    public function testSetWordId()
    {
        $this->entity->setWordId(4);
        $this->assertEquals(4, $this->entity->getWordId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::setWordIdFromWord()
     */
    public function testSetWordIdFromWord()
    {
        $this->entity->setWordIdFromWord(new Word(4, 'test'));
        $this->assertEquals(4, $this->entity->getWordId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::getFieldCount()
     */
    public function testGetFieldCount()
    {
        $this->assertEquals(4, $this->entity->getFieldCount());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::getFieldCount()
     */
    public function testSetFieldCount()
    {
        $this->entity->setFieldCount(5);
        $this->assertEquals(5, $this->entity->getFieldCount());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Term::create(2, 3, 4));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Term::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Term::make(1, 2, 3, 4));
    }
}