<?php

namespace BlixtTests\Models;

use Blixt\Models\Schema;
use Blixt\Models\Term;
use BlixtTests\TestCase;

class TermTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Term(1, 1, 1, 1);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(1, $test->getSchemaId());
        $this->assertEquals(1, $test->getWordId());
        $this->assertEquals(1, $test->getFieldCount());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setSchemaId(3);
        $this->assertEquals(3, $test->getSchemaId());

        $test->setWordId(4);
        $this->assertEquals(4, $test->getWordId());

        $test->setFieldCount(5);
        $this->assertEquals(5, $test->getFieldCount());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Term(1, 1, 1, 1);

        $test->setId('100');
        $this->assertSame(100, $test->getId());

        $test->setSchemaId(100.4);
        $this->assertSame(100, $test->getSchemaId());

        $test->setWordId(true);
        $this->assertSame(1, $test->getWordId());

        $test->setFieldCount('5.56test');
        $this->assertSame(5, $test->getFieldCount());
    }

}