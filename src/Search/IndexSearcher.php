<?php

namespace Blixt\Search;

use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\StorageManager;
use Blixt\Search\Query\Query;
use Blixt\Search\Results\ResultSet;
use Blixt\Tokenization\Tokenizer;

class IndexSearcher
{
    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $schema;

    /**
     * @var \Blixt\Persistence\StorageManager
     */
    protected $storage;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * IndexSearcher constructor.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param \Blixt\Persistence\StorageManager $storage
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function __construct(Schema $schema, StorageManager $storage, Tokenizer $tokenizer)
    {
        $this->schema = $schema;
        $this->storage = $storage;
        $this->tokenizer = $tokenizer;
    }

    public function query(Query $query): ResultSet
    {
    }
}
