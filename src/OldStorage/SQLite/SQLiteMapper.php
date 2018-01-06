<?php

namespace Blixt\Storage\SQLite;

use Blixt\Models\Column;
use Blixt\Models\Document;
use Blixt\Models\Field;
use Blixt\Models\Occurrence;
use Blixt\Models\Presence;
use Blixt\Models\Word;
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

    public function field(array $row)
    {
        return new Field($row['id'], $row['document_id'], $row['column_id'], $row['value']);
    }

    public function fields(array $rows)
    {
        $fields = new Collection();

        foreach ($rows as $row) {
            $fields->push($this->field($row));
        }

        return $fields;
    }

    public function word(array $row)
    {
        return new Word($row['id'], $row['word']);
    }

    public function presence(array $row)
    {
        return new Presence($row['id'], $row['field_id'], $row['word_id'], $row['frequency']);
    }

    public function presences(array $rows)
    {
        $presences = new Collection();

        foreach ($rows as $row) {
            $presences->push($this->presence($row));
        }

        return $presences;
    }

    public function occurrence(array $row)
    {
        return new Occurrence($row['id'], $row['presence_id'], $row['position']);
    }
}