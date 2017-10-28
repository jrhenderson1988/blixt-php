<?php

namespace BlixtTests\Models;

use Blixt\Models\Document;
use Blixt\Models\Occurrence;
use BlixtTests\TestCase;

class OccurrenceTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Occurrence(1, 1, 1);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(1, $test->getPresenceId());
        $this->assertEquals(1, $test->getPosition());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setPresenceId(3);
        $this->assertEquals(3, $test->getPresenceId());

        $test->setPosition(4);
        $this->assertEquals(4, $test->getPosition());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Occurrence(1, 1, 1);

        $test->setId('12');
        $this->assertSame(12, $test->getId());

        $test->setPresenceId(true);
        $this->assertSame(1, $test->getPresenceId());

        $test->setPosition(true);
        $this->assertSame(1, $test->getPosition());
    }

}