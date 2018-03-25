<?php

namespace Blixt\Search\Query;

use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;

interface Query
{
    public function execute(Storage $storage, Tokenizer $tokenizer, Schema $schema);
}
