<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Field;
use BlixtTests\TestCase;

class FieldTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructor()
    {
        $test = new Field(1, 2, 3, 'test');
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getDocumentId());
        $this->assertEquals(3, $test->getColumnId());
        $this->assertEquals('test', $test->getValue());
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testCreateConstructsFieldCorrectlyWithNullId()
    {
        $field = Field::create(1, 2, 'value');
        $constructed = new Field(null, 1, 2, 'value');
        $this->assertEquals($constructed, $field);
        $this->assertNull($field->getId());
        $this->assertEquals(1, $field->getDocumentId());
        $this->assertEquals(2, $field->getColumnId());
        $this->assertEquals('value', $field->getValue());
    }

    /**
     * @test
     */
    public function testMakeConstructsFieldCorrectly()
    {
        $field = Field::make(1, 2, 3, 'value');
        $constructed = new Field(1, 2, 3, 'value');
        $this->assertEquals($constructed, $field);
        $this->assertEquals(1, $field->getId());
        $this->assertEquals(2, $field->getDocumentId());
        $this->assertEquals(3, $field->getColumnId());
        $this->assertEquals('value', $field->getValue());
    }
}