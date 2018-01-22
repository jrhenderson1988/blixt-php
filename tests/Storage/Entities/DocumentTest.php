<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Document;
use BlixtTests\TestCase;

class DocumentTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Document(1, 2, 3);

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getSchemaId());
        $this->assertEquals(3, $test->getKey());
    }

    /** @test */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Document(1, 2, 3);

        $test->setId(3);
        $this->assertEquals(3, $test->getId());

        $test->setSchemaId(1);
        $this->assertEquals(1, $test->getSchemaId());

        $test->setKey(2);
        $this->assertEquals(2, $test->getKey());
    }

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Document(1, 2, 3);

        $test->setId('3');
        $this->assertSame(3, $test->getId());

        $test->setSchemaId(1.4);
        $this->assertSame(1, $test->getSchemaId());

        // Key doesn't cast
        $test->setKey('2');
        $this->assertSame('2', $test->getKey());
    }

}