<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Column;
use BlixtTests\TestCase;

class ColumnTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Column(1, 2, 'test', true, false);

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getSchemaId());
        $this->assertEquals('test', $test->getName());
        $this->assertEquals(true, $test->isIndexed());
        $this->assertEquals(false, $test->isStored());
    }

    /** @test */
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

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Column(1, 2, 'test', true, false);

        $test->setId(true);
        $this->assertSame(1, $test->getId());

        $test->setSchemaId(1.4);
        $this->assertSame(1, $test->getSchemaId());

        $test->setName(1);
        $this->assertSame('1', $test->getName());

        $test->setIndexed(0);
        $this->assertSame(false, $test->isIndexed());

        $test->setStored('1');
        $this->assertSame(true, $test->isStored());
    }

    /** @test */
    public function testMakeMethod()
    {
        $attributes = ['id' => 1, 'schema_id' => 1, 'name' => 'test', 'indexed' => 1, 'stored' => false];
        $column = Column::make($attributes);
        $this->assertSame(1, $column->getId());
        $this->assertSame(1, $column->getSchemaId());
        $this->assertSame('test', $column->getName());
        $this->assertSame(true, $column->isIndexed());
        $this->assertSame(false, $column->isStored());

        $attributes = ['name' => 'test'];
        $column = Column::make($attributes);
        $this->assertSame('test', $column->getName());
        $this->assertNull($column->getId());
        $this->assertNull($column->getSchemaId());
        $this->assertNull($column->isIndexed());
        $this->assertNull($column->isStored());
    }
}