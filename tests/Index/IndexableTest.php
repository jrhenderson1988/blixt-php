<?php

namespace BlixtTests\Index;

use Blixt\Index\Indexable;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class IndexableTest extends TestCase
{
    /** @test */
    public function testDocumentConstructorSetsKey()
    {
        $document = new Indexable(1);
        $this->assertEquals(1, $document->getKey());
    }

    /** @test */
    public function testDocumentConstructorAcceptsNullArrayAndCollectionForFields()
    {
        foreach ([null, [], new Collection()] as $input) {
            $document = new Indexable(1, $input);
            $this->assertEquals(new Collection(), $document->getFields());
        }
    }

    /** @test */
    public function testExceptionIsThrownWhenProvidingInvalidArgumentForFields()
    {
        $this->expectException(InvalidArgumentException::class);
        new Indexable(1, 'test');
    }

    /** @test */
    public function testGettingAndSettingKeyAndEnsureValuesAreCorrectlyCast()
    {
        $document = new Indexable(1);
        $this->assertEquals(1, $document->getKey());

        $document->setKey(2);
        $this->assertEquals(2, $document->getKey());

        $document->setKey(true);
        $this->assertSame(1, $document->getKey());

        $document->setKey(3.4);
        $this->assertSame(3, $document->getKey());
    }

    /** @test */
    public function testSettingAndGettingFieldsAndEnsureACollectionIsAlwaysReturned()
    {
        $document = new Indexable(1);

        $document->setFields([]);
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(new Collection(), $document->getFields());

        $document->setFields(new Collection());
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(new Collection(), $document->getFields());

        $document->setFields(['a' => 'b']);
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(new Collection(['a' => 'b']), $document->getFields());

        $document->setFields(new Collection(['a' => 'b']));
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(new Collection(['a' => 'b']), $document->getFields());
    }

    /** @test */
    public function testSetFieldOverwritesFieldOrAddsToExistingFields()
    {
        $document = new Indexable(1, [
            'a' => '1'
        ]);
        $this->assertEquals(new Collection(['a' => '1']), $document->getFields());

        $document->setField('b', '2');
        $this->assertEquals(new Collection(['a' => '1', 'b' => '2']), $document->getFields());

        $document->setField('c', '3');
        $this->assertEquals(new Collection(['a' => '1', 'b' => '2', 'c' => '3']), $document->getFields());

        $document->setField('a', '3');
        $this->assertEquals(new Collection(['a' => '3', 'b' => '2', 'c' => '3']), $document->getFields());
    }

    /** @test */
    public function testCollectionReturnedFromGetFieldsIsKeyedByInputtedKeys()
    {
        $input = [
            'a' => '1',
            2 => 2,
            'c' => 3.4,
            4 => false
        ];

        $document = new Indexable(1, $input);
        $this->assertInstanceOf(Collection::class, $document->getFields());
        $this->assertEquals(array_keys($input), $document->getFields()->keys()->toArray());

        foreach ($input as $key => $value) {
            $this->assertSame($value, $document->getFields()->get($key));
        }
    }
}