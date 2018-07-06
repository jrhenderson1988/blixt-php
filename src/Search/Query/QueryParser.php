<?php

namespace Blixt\Search\Query;

use Blixt\Tokenization\Tokenizer;

class QueryParser
{
    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * QueryParser constructor.
     *
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    public function parse(string $terms): Query
    {
        return null;
    }
}
