<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Persistence\Entities\Document;
use Blixt\Persistence\Entities\Schema;
use BlixtTests\TestCase;

class DocumentTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Document
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Document(1, 2, 3);
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
     * @covers \Blixt\Persistence\Entities\Document::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::getSchemaId()
     */
    public function testGetSchemaId()
    {
        $this->assertEquals(2, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::setSchemaId()
     */
    public function testSetSchemaId()
    {
        $this->entity->setSchemaId(3);
        $this->assertEquals(3, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::setSchemaIdFromSchema()
     */
    public function testSetSchemaIdFromSchema()
    {
        $this->entity->setSchemaIdFromSchema(new Schema(3, 'foobar'));
        $this->assertEquals(3, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::getKey()
     */
    public function testGetKey()
    {
        $this->assertEquals(3, $this->entity->getKey());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::setKey()
     */
    public function testSetKey()
    {
        $this->entity->setKey(1);
        $this->assertEquals(1, $this->entity->getKey());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Document::create(2, 3));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Document::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Document::make(1, 2, 3));
    }
}