<?php

namespace BlixtTests\Storage\Entities;

use Blixt\Storage\Entities\Document;
use BlixtTests\TestCase;

class DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function testConstructor()
    {
        $test = new Document(1, 2, 3);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getSchemaId());
        $this->assertEquals(3, $test->getKey());
    }

    /**
     * @test
     */
    public function testGettersAndSettersGetAndSetCorrectValues()
    {
        $test = new Document(1, 2, 3);
        $test->setId(3);
        $this->assertEquals(3, $test->getId());
        $test->setSchemaId(1);
        $this->assertEquals(1, $test->getSchemaId());
        $test->setKey(2);
        $this->assertEquals(2, $test->getKey());
    }

    /**
     * @test
     */
    public function testCreateConstructsDocumentCorrectlyWithNullId()
    {
        $document = Document::create(1, 2);
        $constructed = new Document(null, 1, 2);
        $this->assertEquals($constructed, $document);
        $this->assertNull($document->getId());
        $this->assertEquals(1, $document->getSchemaId());
        $this->assertEquals(2, $document->getKey());
    }

    /**
     * @test
     */
    public function testMakeConstructsDocumentCorrectly()
    {
        $document = Document::make(1, 2, 3);
        $constructed = new Document(1, 2, 3);
        $this->assertEquals($constructed, $document);
        $this->assertEquals(1, $document->getId());
        $this->assertEquals(2, $document->getSchemaId());
        $this->assertEquals(3, $document->getKey());
    }
}