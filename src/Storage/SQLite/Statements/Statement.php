<?php

namespace Blixt\Storage\SQLite\Statements;

use Blixt\Exceptions\StorageException;
use Illuminate\Support\Collection;
use PDO;

abstract class Statement
{
    protected $pdo;

    public function __construct(PDO $pdo, $sql, $bindings)
    {
        $this->pdo = $pdo;
    }

    public function execute()
    {
        $prepared = $this->pdo->prepare($this->sql());

        return $prepared->execute($this->bindings());
    }

    protected abstract function sql();
    protected abstract function bindings();
}