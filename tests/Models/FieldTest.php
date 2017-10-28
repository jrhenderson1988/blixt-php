<?php

namespace BlixtTests\Models;

use Blixt\Models\Field;
use BlixtTests\TestCase;

class FieldTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Field(1, 1, 1, 1);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(1, $test->getDocumentId());
        $this->assertEquals(1, $test->getColumnId());
        $this->assertEquals(1, $test->getValue());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setDocumentId(3);
        $this->assertEquals(3, $test->getDocumentId());

        $test->setColumnId(4);
        $this->assertEquals(4, $test->getColumnId());

        $test->setValue(5);
        $this->assertEquals(5, $test->getValue());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Field(1, 1, 1, 1);

        $test->setId(true);
        $this->assertSame(1, $test->getId());

        $test->setDocumentId('1');
        $this->assertSame(1, $test->getDocumentId());

        $test->setColumnId(2.34);
        $this->assertSame(2, $test->getColumnId());

        // No casting.
        foreach (['string', 1, 1.34, true, ['test', 'test', 1]] as $value) {
            $test->setValue($value);
            $this->assertSame($value, $test->getValue());
        }
    }
}