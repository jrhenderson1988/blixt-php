<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Occurrence;
use BlixtTests\TestCase;

class OccurrenceTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Occurrence(1, 2, 3, 100);

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getFieldId());
        $this->assertEquals(3, $test->getTermId());
        $this->assertEquals(100, $test->getFrequency());
    }

    /** @test */
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

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Occurrence(1, 2, 3, 100);

        $test->setId('3');
        $this->assertSame(3, $test->getId());

        $test->setFieldId(2.4);
        $this->assertSame(2, $test->getFieldId());

        $test->setTermId(true);
        $this->assertSame(1, $test->getTermId());

        $test->setFrequency('10');
        $this->assertSame(10, $test->getFrequency());
    }

}