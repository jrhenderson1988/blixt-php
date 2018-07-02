<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Persistence\Entities\Field;
use BlixtTests\TestCase;

class FieldTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Field
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Field(1, 2, 3, 'test');
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
     * @covers \Blixt\Persistence\Entities\Field::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::getDocumentId()
     */
    public function testGetDocumentId()
    {
        $this->assertEquals(2, $this->entity->getDocumentId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::setDocumentId()
     */
    public function testSetDocumentId()
    {
        $this->entity->setDocumentId(3);
        $this->assertEquals(3, $this->entity->getDocumentId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::getColumnId()
     */
    public function testGetColumnId()
    {
        $this->assertEquals(3, $this->entity->getColumnId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::setColumnId()
     */
    public function testSetColumnId()
    {
        $this->entity->setColumnId(1);
        $this->assertEquals(1, $this->entity->getColumnId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::
     */
    public function testGetValue()
    {
        $this->assertEquals('test', $this->entity->getValue());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::
     */
    public function testSetValue()
    {
        foreach (['foobar', 1, true, null] as $value) {
            $this->entity->setValue($value);
            $this->assertEquals($value, $this->entity->getValue());
        }
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Field::create(2, 3, 'test'));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Field::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Field::make(1, 2, 3, 'test'));
    }
}