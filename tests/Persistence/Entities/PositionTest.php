<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Storage\Entities\Position;
use BlixtTests\TestCase;

class PositionTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Position
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Position(1, 2, 3);
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
     * @covers \Blixt\Persistence\Entities\Position::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::getOccurrenceId()
     */
    public function testGetOccurrenceId()
    {
        $this->assertEquals(2, $this->entity->getOccurrenceId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::setOccurrenceId()
     */
    public function testSetOccurrenceId()
    {
        $this->entity->setOccurrenceId(3);
        $this->assertEquals(3, $this->entity->getOccurrenceId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::getPosition()
     */
    public function testGetPosition()
    {
        $this->assertEquals(3, $this->entity->getPosition());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::setPosition()
     */
    public function testSetPosition()
    {
        $this->entity->setPosition(4);
        $this->assertEquals(4, $this->entity->getPosition());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Position::create(2, 3));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Position::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Position::make(1, 2, 3));
    }
}