<?php

namespace Blixt\Persistence\Drivers;

use Blixt\Storage\Entities\Entity;

interface Driver
{
    public function find(string $table, int $key): Entity;
}