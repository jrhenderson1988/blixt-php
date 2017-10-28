<?php

namespace BlixtTests\Models;

use Blixt\Models\Document;
use BlixtTests\TestCase;

class DocumentTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Document(1, 1, 1);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(1, $test->getSchemaId());
        $this->assertEquals(1, $test->getKey());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setSchemaId(3);
        $this->assertEquals(3, $test->getSchemaId());

        $test->setKey(4);
        $this->assertEquals(4, $test->getKey());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Document(1, 1, 1);

        $test->setId('12');
        $this->assertSame(12, $test->getId());

        $test->setSchemaId(true);
        $this->assertSame(1, $test->getSchemaId());

        // No casting.
        foreach (['string', 1, 1.34, true, ['test', 'test', 1]] as $value) {
            $test->setKey($value);
            $this->assertSame($value, $test->getKey());
        }
    }

}