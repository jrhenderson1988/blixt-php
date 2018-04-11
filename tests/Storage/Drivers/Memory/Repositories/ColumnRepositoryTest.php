<?php

namespace BlixtTests\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
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

    /**
     * @test
     */
    public function testGetBySchemaReturnsEmptyCollectionInitially()
    {
        $schema = Schema::make(1, 'test');
        $columns = $this->columns->getBySchema($schema);
        $this->assertInstanceOf(Collection::class, $columns);
        $this->assertEquals(0, $columns->count());
    }

    /**
     * @test
     */
    public function testGetBySchemaReturnsOnlyColumnsWithinSchema()
    {
        $schema1 = Schema::make(1, 'schema1');
        $column1 = $this->columns->save(Column::create($schema1->getId(), 'column1', true, true));
        $column2 = $this->columns->save(Column::create($schema1->getId(), 'column2', true, true));
        $schema2 = Schema::make(2, 'schema2');
        $column3 = $this->columns->save(Column::create($schema2->getId(), 'column3', true, true));

        $bySchema1 = $this->columns->getBySchema($schema1);
        $this->assertTrue($bySchema1->contains($column1));
        $this->assertTrue($bySchema1->contains($column2));
        $this->assertFalse($bySchema1->contains($column3));

        $bySchema2 = $this->columns->getBySchema($schema2);
        $this->assertFalse($bySchema2->contains($column1));
        $this->assertFalse($bySchema2->contains($column2));
        $this->assertTrue($bySchema2->contains($column3));
    }

    /**
     * @test
     */
    public function testSaveCreatesNewColumnWhenColumnEntityHasNoId()
    {
        $column = Column::create(1, 'test', true, true);
        $saved = $this->columns->save($column);
        $this->assertInstanceOf(Column::class, $saved);
        $this->assertEquals($saved->getSchemaId(), $column->getSchemaId());
        $this->assertEquals($saved->getName(), $column->getName());
        $this->assertEquals($saved->isStored(), $column->isStored());
        $this->assertEquals($saved->isIndexed(), $column->isIndexed());
        $this->assertNotNull($saved->getId());
    }

    /**
     * @test
     */
    public function testSaveUpdatesGivenColumn()
    {
        $schema = Schema::make(1, 'test');
        $column = $this->columns->save(Column::create($schema->getId(), 'test 1', true, true));
        $bySchema = $this->columns->getBySchema($schema);
        $this->assertTrue($bySchema->contains($column));
        $this->assertEquals(1, $bySchema->count());

        $updated = clone $column;
        $updated->setName('test 2');
        $saved = $this->columns->save($updated);
        $this->assertEquals('test 2', $saved->getName());
        $this->assertEquals($column->getId(), $saved->getId());
        $bySchema = $this->columns->getBySchema($schema);
        $this->assertTrue($bySchema->contains($saved));
        $this->assertEquals(1, $bySchema->count());
        $this->assertFalse($bySchema->contains($column));
    }
}