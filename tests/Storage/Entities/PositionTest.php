<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Position;
use BlixtTests\TestCase;

class PositionTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructor()
    {
        $test = new Position(1, 2, 3);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getOccurrenceId());
        $this->assertEquals(3, $test->getPosition());
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testCreateConstructsPositionCorrectlyWithNullId()
    {
        $position = Position::create(1, 2);
        $constructed = new Position(null, 1, 2);
        $this->assertEquals($constructed, $position);
        $this->assertNull($position->getId());
        $this->assertEquals(1, $position->getOccurrenceId());
        $this->assertEquals(2, $position->getPosition());
    }

    /**
     * @test
     */
    public function testMakeConstructsPositionCorrectly()
    {
        $position = Position::make(1, 2, 3);
        $constructed = new Position(1, 2, 3);
        $this->assertEquals($constructed, $position);
        $this->assertEquals(1, $position->getId());
        $this->assertEquals(2, $position->getOccurrenceId());
        $this->assertEquals(3, $position->getPosition());
    }
}