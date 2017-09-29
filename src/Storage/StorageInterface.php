<?php

namespace Blixt\Storage;

interface StorageInterface
{
    public function setIndex($index);
    public function getIndex();
    public function exists();
    public function create();
}