<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Column;
use BlixtTests\TestCase;

class ColumnTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructor()
    {
        $test = new Column(1, 2, 'test', true, false);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getSchemaId());
        $this->assertEquals('test', $test->getName());
        $this->assertEquals(true, $test->isIndexed());
        $this->assertEquals(false, $test->isStored());
    }

    /**
     * @test
     */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Column(1, 2, 'test', true, false);
        $test->setId(2);
        $this->assertEquals(2, $test->getId());
        $test->setSchemaId(1);
        $this->assertEquals(1, $test->getSchemaId());
        $test->setName('another_test');
        $this->assertEquals('another_test', $test->getName());
        $test->setIndexed(false);
        $this->assertEquals(false, $test->isIndexed());
        $test->setStored(true);
        $this->assertEquals(true, $test->isStored());
    }

    /**
     * @test
     */
    public function testCreateConstructsColumnCorrectlyWithNullId()
    {
        $column = Column::create(1, 'test', true, false);
        $constructed = new Column(null, 1, 'test', true, false);
        $this->assertEquals($constructed, $column);
        $this->assertNull($column->getId());
        $this->assertEquals(1, $column->getSchemaId());
        $this->assertEquals('test', $column->getName());
        $this->assertTrue($column->isIndexed());
        $this->assertFalse($column->isStored());
    }

    /**
     * @test
     */
    public function testMakeConstructsColumnCorrectly()
    {
        $column = Column::make(1, 2, 'test', true, false);
        $constructed = new Column(1, 2, 'test', true, false);
        $this->assertEquals($constructed, $column);
        $this->assertEquals(1, $column->getId());
        $this->assertEquals(2, $column->getSchemaId());
        $this->assertEquals('test', $column->getName());
        $this->assertTrue($column->isIndexed());
        $this->assertFalse($column->isStored());
    }
}