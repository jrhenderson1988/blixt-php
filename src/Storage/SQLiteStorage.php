<?php

namespace Blixt\Storage;

use InvalidArgumentException;

class SQLiteStorage extends AbstractStorage implements StorageInterface
{
    public function indexExists($name)
    {
        return file_exists($this->path . '/' . $name);
    }

    public function createIndex($name)
    {
        // TODO: Implement createIndex() method.
    }
}