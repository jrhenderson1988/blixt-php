<?php

namespace BlixtTests;

use Blixt\Document\Indexable;
use Blixt\Index;
use Blixt\Indexing\Indexer;
use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Schema;
use Blixt\Search\IndexSearcher;
use Blixt\Search\Query\BooleanQuery;
use Blixt\Search\Query\Clause\ShouldClause;
use Blixt\Search\Query\QueryParser;
use Blixt\Search\Results\ResultSet;
use Illuminate\Support\Collection;
use Mockery as m;

class IndexTest extends TestCase
{
    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $schema;

    /**
     * @var \Blixt\Index
     */
    protected $index;

    /**
     * @var \Mockery\MockInterface|\Blixt\Indexing\Indexer
     */
    protected $indexer;

    /**
     * @var \Mockery\MockInterface|\Blixt\Search\IndexSearcher
     */
    protected $searcher;

    /**
     * @var \Mockery\MockInterface|\Blixt\Search\Query\QueryParser
     */
    protected $parser;

    public function setUp()
    {
        $this->schema = Schema::make(1, 'test');
        $this->schema->setColumns(Collection::make([
            Column::make(1, 1, 'name', true, false),
            Column::make(2, 1, 'age', false, true)
        ]));

        $this->indexer = m::mock(Indexer::class);
        $this->searcher = m::mock(IndexSearcher::class);
        $this->parser = m::mock(QueryParser::class);

        $this->index = new Index($this->schema, $this->indexer, $this->searcher, $this->parser);
    }

    /**
     * @test
     */
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(Index::class, $this->index);
    }

    /**
     * @test
     * @covers \Blixt\Index::add()
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function testAdd()
    {
        $document = new Indexable(1);

        $this->indexer->shouldReceive('add')->withArgs([$document])->andReturnTrue();

        $this->assertTrue($this->index->add($document));
    }

    /**
     * @test
     * @covers \Blixt\Index::query()
     */
    public function testQuery()
    {
        $query = new BooleanQuery(Collection::make([
            new ShouldClause('this'),
            new ShouldClause('is'),
            new ShouldClause('a'),
            new ShouldClause('test')
        ]));

        $this->searcher->shouldReceive('query')->withArgs([$query])->andReturn($resultSet = new ResultSet());

        $this->assertEquals($resultSet, $this->index->query($query));
    }

    /**
     * @test
     * @covers \Blixt\Index::search()
     */
    public function testSearch()
    {
        $search = 'this is a test';
        $query = new BooleanQuery(Collection::make([
            new ShouldClause('this'),
            new ShouldClause('is'),
            new ShouldClause('a'),
            new ShouldClause('test')
        ]));
        $resultSet = new ResultSet();

        $this->parser->shouldReceive('parse')->withArgs([$search])->andReturn($query);
        $this->searcher->shouldReceive('query')->withArgs([$query])->andReturn($resultSet);

        $this->assertEquals($resultSet, $this->index->search($search));
    }
}
