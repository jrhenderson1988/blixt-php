<?php

namespace BlixtTests\Models;

use Blixt\Models\Column;
use BlixtTests\TestCase;

class ColumnTest extends TestCase
{

    public function testGettersAndSetters()
    {
        $test = new Column(1, 'test', true, true, 1.0);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getName());
        $this->assertEquals(true, $test->isIndexed());
        $this->assertEquals(true, $test->isIndexed());
        $this->assertEquals(1.0, $test->isIndexed());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setName('another_test');
        $this->assertEquals('another_test', $test->getName());

        $test->setIndexed(false);
        $this->assertEquals(false, $test->isIndexed());

        $test->setStored(false);
        $this->assertEquals(false, $test->isStored());

        $test->setWeight(2.5);
        $this->assertEquals(2.5, $test->getWeight());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Column(1, 'test', true, true, 1.0);

        $test->setId(true);
        $this->assertSame(1, $test->getId());

        $test->setName(1);
        $this->assertSame('1', $test->getName());

        $test->setIndexed(0);
        $this->assertSame(false, $test->isIndexed());

        $test->setStored('1');
        $this->assertSame(true, $test->isStored());

        $test->setWeight('1.23');
        $this->assertSame(1.23, $test->getWeight());
    }

}