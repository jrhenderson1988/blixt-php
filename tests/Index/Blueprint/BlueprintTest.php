<?php

namespace BlixtTests\Index\Schema;

use Blixt\Index\Blueprint\Blueprint;
use Blixt\Index\Blueprint\Definition;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class BlueprintTest extends TestCase
{
    /** @test */
    public function testBlueprintConstructorSetsName()
    {
        $blueprint = new Blueprint('test');
        $this->assertEquals('test', $blueprint->getName());
    }

    /** @test */
    public function testBlueprintConstructorAcceptsNullArrayAndCollectionForDefinitions()
    {
        foreach ([null, [], new Collection()] as $input) {
            $blueprint = new Blueprint('test', $input);
            $this->assertEquals(new Collection(), $blueprint->getDefinitions());
        }
    }

    /** @test */
    public function testExceptionIsThrownWhenProvidingInvalidArgumentForDefinitions()
    {
        $this->expectException(InvalidArgumentException::class);
        new Blueprint('test', 'test');
    }

    /** @test */
    public function testGettingAndSettingNameAndEnsureValuesAreCorrectlyCast()
    {
        $blueprint = new Blueprint(1);
        $this->assertEquals('1', $blueprint->getName());

        $blueprint->setName(2.34);
        $this->assertEquals('2.34', $blueprint->getName());

        $blueprint->setName(true);
        $this->assertSame('1', $blueprint->getName());
    }

    /** @test */
    public function testSettingAndGettingDefinitionsAndEnsureACollectionIsAlwaysReturned()
    {
        $blueprint = new Blueprint('test');

        $blueprint->setDefinitions([]);
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());
        $this->assertEquals(new Collection(), $blueprint->getDefinitions());

        $blueprint->setDefinitions(new Collection());
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());

        $definition = new Definition('test', true, true);

        $blueprint->setDefinitions([$definition]);
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());

        $blueprint->setDefinitions(new Collection([$definition]));
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());
    }

    /** @test */
    public function testDefinitionsAreAlwaysKeyedByTheirNames()
    {
        $blueprint = new Blueprint('test', [
            new Definition('test1', false, false),
            new Definition('test2', false, true),
            new Definition('test3', true, false),
            new Definition('test4', true, true)
        ]);

        $blueprint->getDefinitions()->each(function (Definition $definition, $key) {
            $this->assertEquals($key, $definition->getName());
        });
    }

    /** @test */
    public function testAddingDefinitionsAcceptsEitherDefinitionInstanceOrParametersToCreateDefinitionInstance()
    {
        $blueprint = new Blueprint('test');

        $definition = new Definition('test', true, false);
        $blueprint->addDefinition($definition);
        $this->assertEquals(new Collection([$definition->getName() => $definition]), $blueprint->getDefinitions());

        $blueprint->setDefinitions([]);
        $this->assertEquals(new Collection(), $blueprint->getDefinitions());

        $blueprint->addDefinition($definition->getName(), $definition->isIndexed(), $definition->isStored());
        $this->assertEquals(new Collection([$definition->getName() => $definition]), $blueprint->getDefinitions());
    }
}