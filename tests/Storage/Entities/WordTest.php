<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Word;
use BlixtTests\TestCase;

class WordTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Word(1, 'test');

        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getWord());
    }

    /** @test */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Word(1, 'test');

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setWord('testing');
        $this->assertEquals('testing', $test->getWord());
    }

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Word(1, 'test');

        $test->setId('34');
        $this->assertSame(34, $test->getId());

        $test->setWord(1);
        $this->assertSame('1', $test->getWord());
        $test->setWord(true);
        $this->assertSame('1', $test->getWord());
    }

}