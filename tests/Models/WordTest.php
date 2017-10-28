<?php


namespace BlixtTests\Models;


use Blixt\Models\Word;
use BlixtTests\TestCase;

class WordTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $test = new Word(1, 'test');
        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getWord());

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setWord('another');
        $this->assertEquals('another', $test->getWord());
    }

    public function testSettersCastToCorrectTypes()
    {
        $test = new Word(1, 'test');

        $test->setId('100');
        $this->assertSame(100, $test->getId());

        $test->setWord(1234);
        $this->assertSame('1234', $test->getWord());
    }

}