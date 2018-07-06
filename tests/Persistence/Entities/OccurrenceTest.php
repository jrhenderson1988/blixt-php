<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Occurrence;
use Blixt\Persistence\Entities\Term;
use BlixtTests\TestCase;

class OccurrenceTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Occurrence
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Occurrence(1, 2, 3, 4);
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
     * @covers \Blixt\Persistence\Entities\Occurrence::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::getFieldId()
     */
    public function testGetFieldId()
    {
        $this->assertEquals(2, $this->entity->getFieldId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::setFieldId()
     */
    public function testSetFieldId()
    {
        $this->entity->setFieldId(3);
        $this->assertEquals(3, $this->entity->getFieldId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::setFieldIdFromField()
     */
    public function testSetFieldIdFromField()
    {
        $this->entity->setFieldIdFromField(new Field(3, 2, 1, 'test'));
        $this->assertEquals(3, $this->entity->getFieldId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::getTermId()
     */
    public function testGetTermId()
    {
        $this->assertEquals(3, $this->entity->getTermId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::setTermId()
     */
    public function testSetTermId()
    {
        $this->entity->setTermId(4);
        $this->assertEquals(4, $this->entity->getTermId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::setTermIdFromTerm()
     */
    public function testSetTermIdFromTerm()
    {
        $this->entity->setTermIdFromTerm(new Term(4, 3, 2, 1));
        $this->assertEquals(4, $this->entity->getTermId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::getFrequency()
     */
    public function testGetFrequency()
    {
        $this->assertEquals(4, $this->entity->getFrequency());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::getFrequency()
     */
    public function testSetFrequency()
    {
        $this->entity->setFrequency(5);
        $this->assertEquals(5, $this->entity->getFrequency());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Occurrence::create(2, 3, 4));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Occurrence::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Occurrence::make(1, 2, 3, 4));
    }
}
