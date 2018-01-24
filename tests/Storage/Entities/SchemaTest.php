<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class SchemaTest extends TestCase
{
    /** @test */
    public function testConstructor()
    {
        $test = new Schema(1, 'test');

        $this->assertEquals(1, $test->getId());
        $this->assertEquals('test', $test->getName());
    }

    /** @test */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Schema(1, 'test');

        $test->setId(2);
        $this->assertEquals(2, $test->getId());

        $test->setName('testing');
        $this->assertEquals('testing', $test->getName());
    }

    /** @test */
    public function testSettersCastToCorrectTypes()
    {
        $test = new Schema(1, 'test');

        $test->setId('34');
        $this->assertSame(34, $test->getId());

        $test->setName(1);
        $this->assertSame('1', $test->getName());
        $test->setName(true);
        $this->assertSame('1', $test->getName());
    }

    /** @test */
    public function testSetColumnsOnlySetsColumnsThatBelongToSchema()
    {
        $columns = new Collection([
            new Column(1, 1, 'column1', true, true),
            new Column(2, 1, 'column2', true, true),
            new Column(3, 1, 'column3', true, true),
            new Column(4, 2, 'column4', true, true)
        ]);

        $schema1 = new Schema(1, 'schema1');
        $schema1->setColumns($columns);

        $this->assertEquals(3, $schema1->getColumns()->count());
        foreach ($schema1->getColumns() as $column) {
            $this->assertInstanceOf(Column::class, $column);
            $this->assertTrue(in_array($column->getName(), ['column1', 'column2', 'column3']));
        }
    }

}