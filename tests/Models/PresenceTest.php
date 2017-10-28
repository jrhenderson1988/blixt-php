<?php

namespace BlixtTests\Models;

use Blixt\Models\Presence;
use BlixtTests\TestCase;

class PresenceTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Presence(1, 1, 1, 1);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(1, $test->getFieldId());
        $this->assertEquals(1, $test->getTermId());
        $this->assertEquals(1, $test->getFrequency());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setFieldId(3);
        $this->assertEquals(3, $test->getFieldId());

        $test->setTermId(4);
        $this->assertEquals(4, $test->getTermId());

        $test->setFrequency(10);
        $this->assertEquals(10, $test->getFrequency());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Presence(1, 1, 1, 1);

        $test->setId(true);
        $this->assertSame(1, $test->getId());

        $test->setFieldId('100');
        $this->assertSame(100, $test->getFieldId());

        $test->setTermId(20.4);
        $this->assertSame(20, $test->getTermId());

        $test->setFrequency('100test');
        $this->assertSame(100, $test->getFrequency());
    }

}