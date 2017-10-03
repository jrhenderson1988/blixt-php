<?php

namespace Blixt\Storage\Drivers;

interface StorageDriverInterface
{
    public function setIndex($index);
    public function getIndex();
    public function exists();
    public function create();
}