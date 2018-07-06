<?php

namespace BlixtTests\Document;

use Blixt\Document\Indexable;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class IndexableTest extends TestCase
{
    /**
     * @test
     */
    public function testDocumentConstructorSetsKey()
    {
        $document = new Indexable(1);
        $this->assertEquals(1, $document->getKey());
    }

    /**
     * @test
     */
    public function testDocumentConstructorAcceptsNothingNullAndCollectionForFields()
    {
        $document1 = new Indexable(1);
        $this->assertEquals(new Collection(), $document1->getFields());

        $document2 = new Indexable(2, null);
        $this->assertEquals(new Collection(), $document2->getFields());

        $document3 = new Indexable(3, new Collection());
        $this->assertEquals(new Collection(), $document3->getFields());
    }

    /**
     * @test
     */
    public function testGettingAndSettingKey()
    {
        $document = new Indexable(1);
        $this->assertEquals(1, $document->getKey());
        $document->setKey(2);
        $this->assertEquals(2, $document->getKey());
    }

    /**
     * @test
     */
    public function testSettingAndGettingFieldsAndEnsureACollectionIsAlwaysReturned()
    {
        $document = new Indexable(1);
        $this->assertInstanceOf(Collection::class, $document->getFields());

        $document->setFields($empty = new Collection([]));
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals($empty, $document->getFields());

        $document->setFields($collection = new Collection(['a' => 'b']));
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals($collection, $document->getFields());
    }

    /** @test */
    public function testSetFieldOverwritesFieldOrAddsToExistingFields()
    {
        $document = new Indexable(1, $initial = new Collection(['a' => '1']));
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals($initial, $document->getFields());

        $document->setField('b', '2');
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(new Collection(['a' => '1', 'b' => '2']), $document->getFields());

        $document->setField('c', '3');
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(new Collection(['a' => '1', 'b' => '2', 'c' => '3']), $document->getFields());

        $document->setField('a', '3');
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(new Collection(['a' => '3', 'b' => '2', 'c' => '3']), $document->getFields());
    }

    /**
     * @test
     */
    public function testGetFieldGetsFieldValue()
    {
        $document = new Indexable(1, new Collection(['a' => 1, 'b' => 2, 'c' => 3]));
        $this->assertEquals(1, $document->getField('a'));
        $this->assertEquals(2, $document->getField('b'));
        $this->assertEquals(3, $document->getField('c'));
    }

    /**
     * @test
     */
    public function testCollectionReturnedFromGetFieldsIsKeyedByInputtedKeys()
    {
        $document = new Indexable(1, $input = new Collection(['a' => '1', 2 => 2, 'c' => 3.4, 4 => false]));
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals($input->keys(), $document->getFields()->keys());
        foreach ($input as $key => $value) {
            $this->assertSame($value, $document->getFields()->get($key));
        }
    }
}