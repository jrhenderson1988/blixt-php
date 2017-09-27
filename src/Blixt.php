<?php

namespace Blixt;

use Blixt\Stemming\StemmerInterface;
use Blixt\Storage\StorageInterface;
use Blixt\Tokenization\TokenizerInterface;

class Blixt
{
    protected $storage;

    public function __construct(StorageInterface $storage, StemmerInterface $stemmer, TokenizerInterface $tokenizer)
    {
        $this->storage = $storage;
    }

    public function open($name)
    {
        return new Index($name, $this->storage);
    }
}