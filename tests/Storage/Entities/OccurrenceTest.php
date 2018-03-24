<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Occurrence;
use BlixtTests\TestCase;

class OccurrenceTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructor()
    {
        $test = new Occurrence(1, 2, 3, 100);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getFieldId());
        $this->assertEquals(3, $test->getTermId());
        $this->assertEquals(100, $test->getFrequency());
    }

    /**
     * @test
     */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Occurrence(1, 2, 3, 100);
        $test->setId(3);
        $this->assertEquals(3, $test->getId());
        $test->setFieldId(1);
        $this->assertEquals(1, $test->getFieldId());
        $test->setTermId(2);
        $this->assertEquals(2, $test->getTermId());
        $test->setFrequency(200);
        $this->assertEquals(200, $test->getFrequency());
    }

    /**
     * @test
     */
    public function testCreateConstructsOccurrenceCorrectlyWithNullId()
    {
        $occurrence = Occurrence::create(1, 2, 3);
        $constructed = new Occurrence(null, 1, 2, 3);
        $this->assertEquals($constructed, $occurrence);
        $this->assertNull($occurrence->getId());
        $this->assertEquals(1, $occurrence->getFieldId());
        $this->assertEquals(2, $occurrence->getTermId());
        $this->assertEquals(3, $occurrence->getFrequency());
    }

    /**
     * @test
     */
    public function testMakeConstructsOccurrenceCorrectly()
    {
        $occurrence = Occurrence::make(1, 2, 3, 4);
        $constructed = new Occurrence(1, 2, 3, 4);
        $this->assertEquals($constructed, $occurrence);
        $this->assertEquals(1, $occurrence->getId());
        $this->assertEquals(2, $occurrence->getFieldId());
        $this->assertEquals(3, $occurrence->getTermId());
        $this->assertEquals(4, $occurrence->getFrequency());
    }
}