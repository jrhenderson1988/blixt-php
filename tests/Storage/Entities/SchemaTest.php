<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Schema;
use BlixtTests\TestCase;

class SchemaTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Schema(1, 'test');

        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getName());
    }

    /** @test */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Schema(1, 'test');

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setName('testing');
        $this->assertEquals('testing', $test->getName());
    }

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Schema(1, 'test');

        $test->setId('34');
        $this->assertSame(34, $test->getId());

        $test->setName(1);
        $this->assertSame('1', $test->getName());
        $test->setName(true);
        $this->assertSame('1', $test->getName());
    }

}