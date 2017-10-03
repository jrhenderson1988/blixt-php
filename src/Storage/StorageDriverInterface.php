<?php

namespace Blixt\Storage;

interface StorageDriverInterface
{
    public function exists();
    public function create();
}