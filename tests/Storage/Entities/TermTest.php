<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Term;
use BlixtTests\TestCase;

class TermTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Term(1, 2, 3, 10);

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getSchemaId());
        $this->assertEquals(3, $test->getWordId());
        $this->assertEquals(10, $test->getFieldCount());
    }

    /** @test */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Term(1, 2, 3, 10);

        $test->setId(3);
        $this->assertEquals(3, $test->getId());

        $test->setSchemaId(1);
        $this->assertEquals(1, $test->getSchemaId());

        $test->setWordId(2);
        $this->assertEquals(2, $test->getWordId());

        $test->setFieldCount(50);
        $this->assertEquals(50, $test->getFieldCount());
    }

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Term(1, 2, 3, 10);

        $test->setId('3');
        $this->assertSame(3, $test->getId());

        $test->setSchemaId(true);
        $this->assertSame(1, $test->getSchemaId());

        $test->setWordId(2.4);
        $this->assertSame(2, $test->getWordId());

        $test->setFieldCount('1000');
        $this->assertSame(1000, $test->getFieldCount());
    }

    /** @test */
    public function testMakeMethod()
    {
        $attributes = ['id' => 1, 'schema_id' => 1, 'word_id' => 1, 'field_count' => 1];
        $term = Term::make($attributes);
        $this->assertSame(1, $term->getId());
        $this->assertSame(1, $term->getSchemaId());
        $this->assertSame(1, $term->getWordId());
        $this->assertSame(1, $term->getFieldCount());

        $attributes = ['field_count' => 2];
        $term = Term::make($attributes);
        $this->assertSame(2, $term->getFieldCount());
        $this->assertNull($term->getId());
        $this->assertNull($term->getSchemaId());
        $this->assertNull($term->getWordId());
    }
}