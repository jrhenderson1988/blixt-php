<?php

namespace Blixt\Index\Search;

use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;

interface Query
{
    public function execute(Storage $storage, Tokenizer $tokenizer, Stemmer $stemmer, Schema $schema);
}
