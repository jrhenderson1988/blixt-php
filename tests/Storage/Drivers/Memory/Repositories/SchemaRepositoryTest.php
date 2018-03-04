<?php

namespace BlixtTests\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Storage;
use Blixt\Storage\Entities\Schema;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class SchemaRepositoryTest extends TestCase
{
    /**
     * @var \Blixt\Storage\Drivers\Memory\Repositories\SchemaRepository
     */
    protected $schemas;

    public function setUp()
    {
        $storage = new Storage();
        $storage->create();

        $this->schemas = $storage->schemas();
    }

    /** @test */
    public function testAllReturnsEmptyCollectionInitially()
    {
        $this->assertEmpty($this->schemas->all());
    }

    /** @test */
    public function testSaveCreatesNewSchemaWhenSchemaEntityHasNoId()
    {
        $schema = new Schema();
        $schema->setName('test');

        $saved = $this->schemas->save($schema);
        $this->assertInstanceOf(Schema::class, $saved);
        $this->assertEquals($saved->getName(), $schema->getName());
        $this->assertNotNull($saved->getId());
    }

    /** @test */
    public function testSaveUpdatesGivenSchema()
    {
        $schema = new Schema();
        $schema->setName('test 1');

        $schema = $this->schemas->save($schema);
        $all = $this->schemas->all();
        $this->assertTrue($all->contains($schema));

        $saved = clone $schema;
        $saved->setName('test 2');

        $this->schemas->save($saved);
        $all = $this->schemas->all();
        $this->assertTrue($all->contains($saved));
        $this->assertFalse($all->contains($schema));
        $this->assertEquals($saved, $all->first());
    }

    /** @test */
    public function testAllReturnsCollectionOfSchemas()
    {
        $schema1 = new Schema();
        $schema1->setName('test 1');

        $schema2 = new Schema();
        $schema2->setName('test 2');

        $schema1 = $this->schemas->save($schema1);
        $schema2 = $this->schemas->save($schema2);

        $all = $this->schemas->all();
        $this->assertInstanceOf(Collection::class, $all);
        $this->assertTrue($all->contains($schema1));
        $this->assertTrue($all->contains($schema2));
    }
}