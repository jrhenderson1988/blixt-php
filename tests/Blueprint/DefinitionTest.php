<?php

namespace BlixtTests\Index\Schema;

use Blixt\Blueprint\Definition;
use BlixtTests\TestCase;

class DefinitionTest extends TestCase
{
    /**
     * @var \Blixt\Blueprint\Definition
     */
    protected $definition;

    public function setUp()
    {
        $this->definition = new Definition('test', true, false);
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Definition::getName()
     */
    public function testGetName()
    {
        $this->assertEquals('test', $this->definition->getName());
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Definition::isIndexed()
     */
    public function testIsIndexed()
    {
        $this->assertTrue($this->definition->isIndexed());
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Definition::isStored()
     */
    public function testIsStored()
    {
        $this->assertFalse($this->definition->isStored());
    }
}
