<?php

namespace BlixtTests\Models;

use Blixt\Models\Document;
use BlixtTests\TestCase;

class DocumentTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Document(1, 1);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(1, $test->getKey());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setKey(4);
        $this->assertEquals(4, $test->getKey());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Document(1, 1);

        $test->setId('12');
        $this->assertSame(12, $test->getId());

        // No casting.
        foreach (['string', 1, 1.34, true, ['test', 'test', 1]] as $value) {
            $test->setKey($value);
            $this->assertSame($value, $test->getKey());
        }
    }

}