<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Schema;
use BlixtTests\TestCase;

class ColumnTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Column
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Column(1, 2, 'test', true, false);
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
     * @covers \Blixt\Persistence\Entities\Column::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::getSchemaId()
     */
    public function testGetSchemaId()
    {
        $this->assertEquals(2, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::getSchemaId()
     */
    public function testSetSchemaId()
    {
        $this->entity->setSchemaId(3);
        $this->assertEquals(3, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::setSchemaIdFromSchema()
     */
    public function testSetSchemaIdFromSchema()
    {
        $this->entity->setSchemaIdFromSchema(new Schema(3, 'foobar'));
        $this->assertEquals(3, $this->entity->getSchemaId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::getName()
     */
    public function testGetName()
    {
        $this->assertEquals('test', $this->entity->getName());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::setName()
     */
    public function testSetName()
    {
        $this->entity->setName('foobar');
        $this->assertEquals('foobar', $this->entity->getName());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::isIndexed()
     */
    public function testIsIndexed()
    {
        $this->assertEquals(true, $this->entity->isIndexed());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::setIndexed()
     */
    public function testSetIndexed()
    {
        $this->entity->setIndexed(false);
        $this->assertFalse($this->entity->isIndexed());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::isStored()
     */
    public function testIsStored()
    {
        $this->assertEquals(false, $this->entity->isStored());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::setStored()
     */
    public function testSetStored()
    {
        $this->entity->setStored(true);
        $this->assertTrue($this->entity->isStored());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Column::create(2, 'test', true, false));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Column::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Column::make(1, 2, 'test', true, false));
    }
}
