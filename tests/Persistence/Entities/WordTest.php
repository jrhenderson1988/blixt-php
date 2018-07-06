<?php

namespace BlixtTests\Persistence\Entities;

use Blixt\Persistence\Entities\Word;
use BlixtTests\TestCase;

class WordTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Word
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Word(1, 'test');
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertNotNull($this->entity);
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Word::getId()
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Word::setId()
     */
    public function testSetId()
    {
        $this->entity->setId(2);
        $this->assertEquals(2, $this->entity->getId());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Word::getWord()
     */
    public function testGetWord()
    {
        $this->assertEquals('test', $this->entity->getWord());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Word::setWord()
     */
    public function testSetWord()
    {
        $this->entity->setWord('foobar');
        $this->assertEquals('foobar', $this->entity->getWord());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Word::exists()
     */
    public function testExists()
    {
        $this->assertTrue($this->entity->exists());
        $this->entity->setId(null);
        $this->assertFalse($this->entity->exists());
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Word::create()
     */
    public function testCreate()
    {
        $this->entity->setId(null);
        $this->assertEquals($this->entity, Word::create('test'));
    }

    /**
     * @test
     * @covers \Blixt\Persistence\Entities\Word::make()
     */
    public function testMake()
    {
        $this->assertEquals($this->entity, Word::make(1, 'test'));
    }
}
