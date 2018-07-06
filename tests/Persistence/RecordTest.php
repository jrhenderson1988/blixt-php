<?php

namespace BlixtTests\Persistence;

use Blixt\Persistence\Record;
use BlixtTests\TestCase;

class RecordTest extends TestCase
{
    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertNotNull(new Record(1, []));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Record::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(123, (new Record(123, []))->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Record::getAttributes()
     */
    public function testGetAttributes()
    {
        $this->assertEquals(['foo' => 'bar'], (new Record(123, ['foo' => 'bar']))->getAttributes());
    }
}
