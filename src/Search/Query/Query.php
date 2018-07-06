<?php

namespace Blixt\Search\Query;

use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\StorageManager;
use Blixt\Tokenization\Tokenizer;

interface Query
{
    /**
     * @return mixed
     */
    public function execute();

    /**
     * Set the storage engine.
     *
     * @param \Blixt\Persistence\StorageManager $storage
     */
    public function setStorage(StorageManager $storage): void;

    /**
     * Set the tokenizer.
     *
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function setTokenizer(Tokenizer $tokenizer): void;

    /**
     * Set the schema.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     */
    public function setSchema(Schema $schema): void;
}
