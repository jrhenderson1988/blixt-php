<?php

namespace BlixtTests\Index\Schema;

use Blixt\Blueprint\Blueprint;
use Blixt\Blueprint\Definition;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class BlueprintTest extends TestCase
{
    /**
     * @var \Blixt\Blueprint\Blueprint
     */
    protected $empty;

    /**
     * @var \Blixt\Blueprint\Blueprint
     */
    protected $withDefinitions;

    public function setUp()
    {
        $this->empty = new Blueprint('empty');
        $this->withDefinitions = new Blueprint('withDefinitions', Collection::make([
            new Definition('test', true, false)
        ]));
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(Blueprint::class, $this->empty);
        $this->assertInstanceOf(Blueprint::class, $this->withDefinitions);
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::getName()
     */
    public function testGetName()
    {
        $this->assertEquals('empty', $this->empty->getName());
        $this->assertEquals('withDefinitions', $this->withDefinitions->getName());
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::getDefinitions()
     */
    public function testGetDefinitions()
    {
        $this->assertInstanceOf(Collection::class, $this->empty->getDefinitions());
        $this->assertEquals(0, $this->empty->getDefinitions()->count());

        $this->assertInstanceOf(Collection::class, $this->withDefinitions->getDefinitions());
        $this->assertEquals(1, $this->withDefinitions->getDefinitions()->count());
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::getDefinitions()
     */
    public function testDefinitionsAreKeyedByName()
    {
        $this->assertInstanceOf(Definition::class, $this->withDefinitions->getDefinitions()->get('test'));
        $this->assertEquals(
            $this->withDefinitions->getDefinitions()->first(),
            $this->withDefinitions->getDefinitions()->get('test')
        );
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::setName()
     */
    public function testSetName()
    {
        $this->assertEquals('empty', $this->empty->getName());
        $this->empty->setName('updated');
        $this->assertEquals('updated', $this->empty->getName());
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::setDefinitions()
     */
    public function testSetDefinitions()
    {
        $this->assertInstanceOf(Collection::class, $this->empty->getDefinitions());
        $this->assertEquals(0, $this->empty->getDefinitions()->count());
        $definitions = Collection::make([
            $first = new Definition('first', true, false),
            $second = new Definition('second', false, true)
        ]);

        $this->empty->setDefinitions($definitions);
        $this->assertInstanceOf(Collection::class, $this->empty->getDefinitions());
        $this->assertEquals(2, $this->empty->getDefinitions()->count());
        $this->assertEquals($first, $this->empty->getDefinitions()->get('first'));
        $this->assertEquals($second, $this->empty->getDefinitions()->get('second'));
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::setDefinitions()
     */
    public function testSetDefinitionsOverwritesDuplicateDefinitions()
    {
        $this->assertInstanceOf(Collection::class, $this->empty->getDefinitions());
        $this->assertEquals(0, $this->empty->getDefinitions()->count());
        $definitions = Collection::make([
            $first = new Definition('first', true, true),
            $second = new Definition('first', false, false)
        ]);

        $this->empty->setDefinitions($definitions);
        $this->assertInstanceOf(Collection::class, $this->empty->getDefinitions());
        $this->assertEquals(1, $this->empty->getDefinitions()->count());
        $this->assertEquals($second, $this->empty->getDefinitions()->get('first'));
        $this->assertFalse($this->empty->getDefinitions()->contains($first));
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::createDefinition()
     */
    public function testCreateDefinitionAddsNewDefinitions()
    {
        $this->assertInstanceOf(Collection::class, $this->empty->getDefinitions());
        $this->assertEquals(0, $this->empty->getDefinitions()->count());

        $this->empty->createDefinition('foo', true, false);
        $this->assertEquals(1, $this->empty->getDefinitions()->count());
        $this->assertInstanceOf(Definition::class, $this->empty->getDefinitions()->get('foo'));
        $this->assertEquals(new Definition('foo', true, false), $this->empty->getDefinitions()->get('foo'));

        $this->empty->createDefinition('bar', false, true);
        $this->assertEquals(2, $this->empty->getDefinitions()->count());
        $this->assertInstanceOf(Definition::class, $this->empty->getDefinitions()->get('bar'));
        $this->assertEquals(new Definition('bar', false, true), $this->empty->getDefinitions()->get('bar'));
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::createDefinition()
     */
    public function testCreateDefinitionOverwritesExistingDefinitions()
    {
        $this->assertInstanceOf(Collection::class, $this->withDefinitions->getDefinitions());
        $this->assertEquals(1, $this->withDefinitions->getDefinitions()->count());
        $original = $this->withDefinitions->getDefinitions()->get('test');
        $this->assertInstanceOf(Definition::class, $original);

        $this->withDefinitions->createDefinition('test', false, true);
        $this->assertEquals(1, $this->withDefinitions->getDefinitions()->count());
        $this->assertNotEquals($original, $this->withDefinitions->getDefinitions()->get('test'));
        $this->assertEquals(new Definition('test', false, true), $this->withDefinitions->getDefinitions()->get('test'));
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::addDefinition()
     */
    public function testAddDefinitionAddsNewDefinitions()
    {
        $this->assertInstanceOf(Collection::class, $this->empty->getDefinitions());
        $this->assertEquals(0, $this->empty->getDefinitions()->count());

        $foo = new Definition('foo', true, true);
        $this->empty->addDefinition($foo);
        $this->assertEquals(1, $this->empty->getDefinitions()->count());
        $this->assertInstanceOf(Definition::class, $this->empty->getDefinitions()->get('foo'));
        $this->assertEquals($foo, $this->empty->getDefinitions()->get('foo'));

        $bar = new Definition('bar', false, false);
        $this->empty->addDefinition($bar);
        $this->assertEquals(2, $this->empty->getDefinitions()->count());
        $this->assertInstanceOf(Definition::class, $this->empty->getDefinitions()->get('bar'));
        $this->assertEquals($bar, $this->empty->getDefinitions()->get('bar'));
    }

    /**
     * @test
     * @covers \Blixt\Blueprint\Blueprint::addDefinition()
     */
    public function testAddDefinitionOverwritesExistingDefinitions()
    {
        $this->assertInstanceOf(Collection::class, $this->withDefinitions->getDefinitions());
        $this->assertEquals(1, $this->withDefinitions->getDefinitions()->count());
        $original = $this->withDefinitions->getDefinitions()->get('test');
        $this->assertInstanceOf(Definition::class, $original);

        $new = new Definition('test', false, false);
        $this->withDefinitions->addDefinition($new);
        $this->assertEquals(1, $this->withDefinitions->getDefinitions()->count());
        $this->assertNotEquals($original, $this->withDefinitions->getDefinitions()->get('test'));
        $this->assertEquals($new, $this->withDefinitions->getDefinitions()->get('test'));
    }
}
