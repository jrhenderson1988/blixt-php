<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Term;
use BlixtTests\TestCase;

class TermTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructorSetsCorrectFields()
    {
        $test = new Term(1, 2, 3, 10);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getSchemaId());
        $this->assertEquals(3, $test->getWordId());
        $this->assertEquals(10, $test->getFieldCount());
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testCreateConstructsTermCorrectlyWithNullId()
    {
        $term = Term::create(1, 2, 3);
        $constructed = new Term(null, 1, 2, 3);
        $this->assertEquals($constructed, $term);
        $this->assertNull($term->getId());
        $this->assertEquals(1, $term->getSchemaId());
        $this->assertEquals(2, $term->getWordId());
        $this->assertEquals(3, $term->getFieldCount());
    }

    /**
     * @test
     */
    public function testMakeConstructsTermCorrectly()
    {
        $term = Term::make(1, 2, 3, 4);
        $constructed = new Term(1, 2, 3, 4);
        $this->assertEquals($constructed, $term);
        $this->assertEquals(1, $term->getId());
        $this->assertEquals(2, $term->getSchemaId());
        $this->assertEquals(3, $term->getWordId());
        $this->assertEquals(4, $term->getFieldCount());
    }
}