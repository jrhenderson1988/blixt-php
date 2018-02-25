<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Position;
use BlixtTests\TestCase;

class PositionTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Position(1, 2, 3);

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getOccurrenceId());
        $this->assertEquals(3, $test->getPosition());
    }

    /** @test */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Position(1, 2, 3);

        $test->setId(3);
        $this->assertEquals(3, $test->getId());

        $test->setOccurrenceId(1);
        $this->assertEquals(1, $test->getOccurrenceId());

        $test->setPosition(2);
        $this->assertEquals(2, $test->getPosition());
    }

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Position(1, 2, 3);

        $test->setId('3');
        $this->assertSame(3, $test->getId());

        $test->setOccurrenceId(2.4);
        $this->assertSame(2, $test->getOccurrenceId());

        $test->setPosition(true);
        $this->assertSame(1, $test->getPosition());
    }

    /** @test */
    public function testMakeMethod()
    {
        $attributes = ['id' => 1, 'occurrence_id' => 1, 'position' => 1];
        $position = Position::make($attributes);
        $this->assertSame(1, $position->getId());
        $this->assertSame(1, $position->getOccurrenceId());
        $this->assertSame(1, $position->getPosition());

        $attributes = ['position' => '1'];
        $position = Position::make($attributes);
        $this->assertSame(1, $position->getPosition());
        $this->assertNull($position->getId());
        $this->assertNull($position->getOccurrenceId());
    }
}