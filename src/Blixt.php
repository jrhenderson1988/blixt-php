<?php

namespace Blixt;

use Blixt\Storage\StorageInterface;

class Blixt
{
    protected $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function open($name)
    {
        return new Index($name, $this->storage);
    }
}