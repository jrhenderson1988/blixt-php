<?php

namespace Blixt;

use Blixt\Document\Indexable;
use Blixt\Indexing\Indexer;
use Blixt\Search\IndexSearcher;
use Blixt\Search\Query\Query;
use Blixt\Persistence\Entities\Schema;
use Blixt\Search\Query\QueryParser;
use Blixt\Search\Results\ResultSet;

class Index
{
    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $schema;

    /**
     * @var \Blixt\Indexing\Indexer|null
     */
    protected $indexer;

    /**
     * @var \Blixt\Search\IndexSearcher|null
     */
    protected $searcher;

    /**
     * @var \Blixt\Search\Query\QueryParser|null
     */
    protected $queryParser;

    /**
     * Index constructor.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param \Blixt\Indexing\Indexer $indexer
     * @param \Blixt\Search\IndexSearcher $searcher
     * @param \Blixt\Search\Query\QueryParser $queryParser
     */
    public function __construct(Schema $schema, Indexer $indexer, IndexSearcher $searcher, QueryParser $queryParser)
    {
        $this->schema = $schema;
        $this->indexer = $indexer;
        $this->searcher = $searcher;
        $this->queryParser = $queryParser;
    }

    /**
     * @param \Blixt\Document\Indexable $indexable
     *
     * @return bool
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\StorageException
     */
    public function add(Indexable $indexable): bool
    {
        return $this->indexer->add($indexable);
    }

    /**
     * Translate the given string of search terms into a query object and run it.
     *
     * @param string $terms
     *
     * @return \Blixt\Search\Results\ResultSet
     */
    public function search(string $terms): ResultSet
    {
        return $this->query(
            $this->queryParser->parse($terms)
        );
    }

    /**
     * @param \Blixt\Search\Query\Query $query
     *
     * @return \Blixt\Search\Results\ResultSet
     */
    public function query(Query $query): ResultSet
    {
        return $this->searcher->search($query);
    }
}
