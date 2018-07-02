<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Persistence\Entities\Schema;
use BlixtTests\TestCase;

class SchemaTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Schema(1, 'test');
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
     * @covers \Blixt\Persistence\Entities\Schema::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Schema::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Schema::getName()
     */
    public function testGetName()
    {
        $this->assertEquals('test', $this->entity->getName());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Schema::setName()
     */
    public function testSetName()
    {
        $this->entity->setName('foobar');
        $this->assertEquals('foobar', $this->entity->getName());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Schema::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Schema::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Schema::create('test'));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Schema::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Schema::make(1, 'test'));
    }
}