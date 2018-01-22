<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Field;
use BlixtTests\TestCase;

class FieldTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Field(1, 2, 3, 'test');

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getDocumentId());
        $this->assertEquals(3, $test->getColumnId());
        $this->assertEquals('test', $test->getValue());
    }

    /** @test */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Field(1, 2, 3, 'test');

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setDocumentId(3);
        $this->assertEquals(3, $test->getDocumentId());

        $test->setColumnId(4);
        $this->assertEquals(4, $test->getColumnId());

        $test->setValue('testing');
        $this->assertEquals('testing', $test->getValue());
    }

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Field(1, 2, 3, 'test');

        $test->setId('3');
        $this->assertSame(3, $test->getId());

        $test->setDocumentId(1.4);
        $this->assertSame(1, $test->getDocumentId());

        $test->setColumnId(true);
        $this->assertSame(1, $test->getColumnId());

        // Value doesn't cast.
        $test->setValue('testing');
        $this->assertSame('testing', $test->getValue());
        $test->setValue(123);
        $this->assertSame(123, $test->getValue());
        $test->setValue(null);
        $this->assertNull(null, $test->getValue());
    }

    /** @test */
    public function testDefaultValueIsNull()
    {
        $field = new Field(1, 2, 3);
        $this->assertNull($field->getValue());
    }
}