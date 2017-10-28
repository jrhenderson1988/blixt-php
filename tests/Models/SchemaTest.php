<?php

namespace BlixtTests\Models;

use Blixt\Models\Schema;
use BlixtTests\TestCase;

class SchemaTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Schema(1, 'test');
        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getName());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setName('another_test');
        $this->assertEquals('another_test', $test->getName());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Schema(1, 'test');

        $test->setId('100');
        $this->assertSame(100, $test->getId());

        $test->setName(1234);
        $this->assertSame('1234', $test->getName());
    }

}