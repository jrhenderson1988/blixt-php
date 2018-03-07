<?php

namespace Blixt\Search;

use Blixt\Index\IndexReader;

class IndexSearcher
{
    /**
     * @var \Blixt\Index\IndexReader
     */
    protected $reader;

    /**
     * IndexSearcher constructor.
     *
     * @param \Blixt\Index\IndexReader $reader
     */
    public function __construct(IndexReader $reader)
    {
        $this->reader = $reader;
    }
}