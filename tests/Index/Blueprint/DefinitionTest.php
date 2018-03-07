<?php

namespace BlixtTests\Index\Schema;

use Blixt\Index\Blueprint\Definition;
use BlixtTests\TestCase;

class DefinitionTest extends TestCase
{
    /** @test */
    public function testDefinitionConstructorSetsCorrectValues()
    {
        $definition = new Definition('test', true, true);
        $this->assertEquals('test', $definition->getName());
        $this->assertEquals(true, $definition->isIndexed());
        $this->assertEquals(true, $definition->isStored());
    }

    /** @test */
    public function testDefinitionConstructorCastsValuesToCorrectTypes()
    {
        $definition = new Definition(1.34, 1, '0');
        $this->assertSame('1.34', $definition->getName());
        $this->assertSame(true, $definition->isIndexed());
        $this->assertSame(false, $definition->isStored());
    }
}