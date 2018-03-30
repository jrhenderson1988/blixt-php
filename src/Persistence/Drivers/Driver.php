<?php

namespace Blixt\Persistence\Drivers;

interface Driver
{
    public function find(string $table, int $key): ?array;
    public function findBy(string $table, int $field, $value): ?array;
    public function getWhere(string $table, array $conditions, int $offset = 0, ?int $limit = null): ?array;
    public function create(string $table, array $attributes): array;
    public function update(string $table, int $key, array $attributes): array;
}