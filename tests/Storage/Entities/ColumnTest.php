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

}