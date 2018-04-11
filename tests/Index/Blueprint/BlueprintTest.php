<?php

namespace BlixtTests\Index\Schema;

use Blixt\Index\Blueprint\Blueprint;
use Blixt\Index\Blueprint\Definition;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class BlueprintTest extends TestCase
{
    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $blueprint = new Blueprint('test');
        $this->assertInstanceOf(Blueprint::class, $blueprint);
    }

    /**
     * @test
     */
    public function testBlueprintConstructorSetsNameAndEmptyCollection()
    {
        $blueprint = new Blueprint('test');
        $this->assertEquals('test', $blueprint->getName());
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());
        $this->assertEquals(0, $blueprint->getDefinitions()->count());
    }

    /**
     * @test
     */
    public function testBlueprintConstructorSetsDefinitionsFromCollection()
    {
        $collection = new Collection([$definition = new Definition('test', true, false)]);
        $blueprint = new Blueprint('test', $collection);
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());
        $this->assertTrue($blueprint->getDefinitions()->contains($definition));
        $this->assertEquals($blueprint->getDefinitions()->get('test'), $definition);
    }

    /**
     * @test
     */
    public function testSettingAndGettingNameWorksSetsAndGetsTheNameValueCorrectly()
    {
        $blueprint = new Blueprint('test');
        $this->assertEquals('test', $blueprint->getName());
        $blueprint->setName('test1');
        $this->assertEquals('test1', $blueprint->getName());
    }

    /**
     * @test
     */
    public function testSettingDefinitionsCreatesCollectionKeyedByDefinitionNames()
    {
        $blueprint = new Blueprint('test');
        $definitions = new Collection([$definition1 = new Definition('test1', true, false), $definition2 = new Definition('test2', false, true)]);
        $blueprint->setDefinitions($definitions);
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());
        $this->assertEquals($definitions->count(), $blueprint->getDefinitions()->count());
        $this->assertTrue($blueprint->getDefinitions()->contains($definition1));
        $this->assertEquals($definition1, $blueprint->getDefinitions()->get($definition1->getName()));
        $this->assertTrue($blueprint->getDefinitions()->contains($definition2));
        $this->assertEquals($definition2, $blueprint->getDefinitions()->get($definition2->getName()));
    }

    /**
     * @test
     */
    public function testCreatingDefinitionsAddsToCollectionAndKeysByName()
    {
        $blueprint = new Blueprint('test');
        $blueprint->createDefinition('test', true, false);
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());
        $this->assertEquals(1, $blueprint->getDefinitions()->count());
        $this->assertEquals('test', $blueprint->getDefinitions()->keys()->first());
    }

    /**
     * @test
     */
    public function testAddingDefinitionsAddsToCollectionAndKeysByName()
    {
        $blueprint = new Blueprint('test');
        $blueprint->addDefinition($definition = new Definition('test', true, false));
        $this->assertInstanceOf(Collection::class, $blueprint->getDefinitions());
        $this->assertEquals(1, $blueprint->getDefinitions()->count());
        $this->assertEquals('test', $blueprint->getDefinitions()->keys()->first());
        $this->assertEquals($definition, $blueprint->getDefinitions()->first());
    }

    /**
     * @test
     */
    public function testDefinitionsAreAlwaysKeyedByTheirNames()
    {
        $blueprint = new Blueprint('test', new Collection([
            new Definition('test1', false, false),
            new Definition('test2', false, true),
            new Definition('test3', true, false),
            new Definition('test4', true, true)
        ]));

        $blueprint->getDefinitions()->each(function (Definition $definition, $key) {
            $this->assertEquals($key, $definition->getName());
        });
    }
}