<?php

namespace Blixt\Index;

use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Tokenizer;

class IndexWriter
{
    public function __construct(Storage $storage, Tokenizer $tokenizer, Stemmer $stemmer, Schema $schema)
    {

    }
}