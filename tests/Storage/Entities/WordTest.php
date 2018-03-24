<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Word;
use BlixtTests\TestCase;

class WordTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructorSetsCorrectFields()
    {
        $test = new Word(1, 'test');
        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getWord());
    }

    /**
     * @test
     */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Word(1, 'test');
        $test->setId(2);
        $this->assertEquals(2, $test->getId());
        $test->setWord('testing');
        $this->assertEquals('testing', $test->getWord());
    }

    /**
     * @test
     */
    public function testCreateConstructsWordCorrectlyWithNullId()
    {
        $word = Word::create('test');
        $constructed = new Word(null, 'test');
        $this->assertEquals($constructed, $word);
        $this->assertNull($word->getId());
        $this->assertEquals('test', $word->getWord());
    }

    /**
     * @test
     */
    public function testMakeConstructsWordCorrectly()
    {
        $word = Word::make(1, 'test');
        $constructed = new Word(1, 'test');
        $this->assertEquals($word, $constructed);
        $this->assertEquals(1, $word->getId());
        $this->assertEquals('test', $word->getWord());
    }
}