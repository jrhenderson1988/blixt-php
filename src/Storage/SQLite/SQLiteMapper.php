<?php

namespace Blixt\Storage\SQLite;

use Blixt\Models\Column;
use Blixt\Models\Document;
use Illuminate\Support\Collection;

class SQLiteMapper
{
    public function column(array $row)
    {
        return new Column(
            $row['id'], $row['name'], $row['indexed'], $row['stored'], $row['weight']
        );
    }

    public function columns(array $rows)
    {
        $columns = new Collection();

        foreach ($rows as $row) {
            $columns->push($this->column($row));
        }

        return $columns;
    }

    public function document(array $row)
    {
        return new Document($row['id'], $row['key']);
    }
}