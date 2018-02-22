<?php

namespace BlixtTests\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Column;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class ColumnRepositoryTest extends TestCase
{
    /**
     * @var \Blixt\Storage\Drivers\Memory\Repositories\ColumnRepository
     */
    protected $columns;

    public function setUp()
    {
        $storage = new Storage();
        $storage->create();
        $this->columns = $storage->columns();
    }

    /** @test */
    public function testAllReturnsEmptyCollectionInitially()
    {
        $this->assertEmpty($this->columns->all());
    }

    /** @test */
    public function testSaveCreatesNewColumnWhenColumnEntityHasNoId()
    {
        $column = new Column();
        $column->setSchemaId(1);
        $column->setName('test');
        $column->setStored(true);
        $column->setIndexed(true);

        $saved = $this->columns->save($column);
        $this->assertInstanceOf(Column::class, $saved);
        $this->assertEquals($saved->getSchemaId(), $column->getSchemaId());
        $this->assertEquals($saved->getName(), $column->getName());
        $this->assertEquals($saved->isStored(), $column->isStored());
        $this->assertEquals($saved->isIndexed(), $column->isIndexed());
        $this->assertNotNull($saved->getId());
    }

    /** @test */
    public function testSaveUpdatesGivenColumn()
    {
        $column = new Column();
        $column->setSchemaId(1);
        $column->setName('test 1');
        $column->setStored(true);
        $column->setIndexed(true);

        $column = $this->columns->save($column);
        $all = $this->columns->all();
        $this->assertTrue($all->contains($column));

        $saved = clone $column;
        $saved->setName('test 2');

        $this->columns->save($saved);
        $all = $this->columns->all();
        $this->assertTrue($all->contains($saved));
        $this->assertFalse($all->contains($column));
        $this->assertEquals($saved, $all->first());
    }

    /** @test */
    public function testAllReturnsCollectionOfColumns()
    {
        $column1 = new Column();
        $column1->setSchemaId(1);
        $column1->setName('test 1');
        $column1->setStored(true);
        $column1->setIndexed(true);

        $column2 = new Column();
        $column2->setSchemaId(1);
        $column2->setName('test 2');
        $column2->setStored(true);
        $column2->setIndexed(true);

        $column1 = $this->columns->save($column1);
        $column2 = $this->columns->save($column2);

        $all = $this->columns->all();
        $this->assertInstanceOf(Collection::class, $all);
        $this->assertTrue($all->contains($column1));
        $this->assertTrue($all->contains($column2));
    }
}