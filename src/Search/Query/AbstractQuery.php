<?php

namespace Blixt\Search\Query;

use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\StorageManager;
use Blixt\Tokenization\Tokenizer;

abstract class AbstractQuery
{
    /**
     * @var \Blixt\Persistence\StorageManager
     */
    protected $storage;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $schema;

    /**
     * @param \Blixt\Persistence\StorageManager $storage
     */
    public function setStorage(StorageManager $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function setTokenizer(Tokenizer $tokenizer): void
    {
        $this->tokenizer = $tokenizer;
    }

    /**
     * @param \Blixt\Persistence\Entities\Schema $schema
     */
    public function setSchema(Schema $schema): void
    {
        $this->schema = $schema;
    }
}