<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class SchemaTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructorSetsCorrectFields()
    {
        $test = new Schema(1, 'test');
        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getName());
    }

    /**
     * @test
     */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Schema(1, 'test');
        $test->setId(2);
        $this->assertEquals(2, $test->getId());
        $test->setName('testing');
        $this->assertEquals('testing', $test->getName());
    }

    /**
     * @test
     */
    public function testSetColumnsOnlySetsColumnsThatBelongToSchema()
    {
        $columns = new Collection([
            $column1 = new Column(1, 1, 'column1', true, true),
            $column2 = new Column(2, 1, 'column2', true, true),
            $column3 = new Column(3, 1, 'column3', true, true),
            $column4 = new Column(4, 2, 'column4', true, true)
        ]);
        $schema1 = new Schema(1, 'schema1');
        $schema1->setColumns($columns);
        $this->assertEquals(3, $schema1->getColumns()->count());
        $this->assertTrue($schema1->getColumns()->contains($column1));
        $this->assertTrue($schema1->getColumns()->contains($column2));
        $this->assertTrue($schema1->getColumns()->contains($column3));
        $this->assertFalse($schema1->getColumns()->contains($column4));
    }

    /**
     * @test
     */
    public function testCreateConstructsSchemaCorrectlyWithNullId()
    {
        $schema = Schema::create('test');
        $constructed = new Schema(null, 'test');
        $this->assertEquals($constructed, $schema);
        $this->assertNull($schema->getId());
        $this->assertEquals('test', $schema->getName());
    }

    /**
     * @test
     */
    public function testMakeConstructsSchemaCorrectly()
    {
        $schema = Schema::make(1, 'test');
        $constructed = new Schema(1, 'test');
        $this->assertEquals($constructed, $schema);
        $this->assertEquals(1, $schema->getId());
        $this->assertEquals('test', $schema->getName());
    }
}