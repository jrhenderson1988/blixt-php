<?php

namespace Blixt\Search\Query;

use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
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
     * @param \Blixt\Storage\Storage $storage
     */
    public function setStorage(Storage $storage): void;

    /**
     * Set the tokenizer.
     *
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function setTokenizer(Tokenizer $tokenizer): void;

    /**
     * Set the schema.
     *
     * @param \Blixt\Storage\Entities\Schema $schema
     */
    public function setSchema(Schema $schema): void;
}
