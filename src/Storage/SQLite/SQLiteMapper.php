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
    /**
     * Create a Column object with the given row data.
     *
     * @param array $row
     *
     * @return \Blixt\Models\Column
     */
    public function column(array $row)
    {
        return new Column(
            $row['id'], $row['name'], $row['indexed'], $row['stored'], $row['weight']
        );
    }

    /**
     * Create a collection of Column objects with the given set of rows.
     *
     * @param array $rows
     *
     * @return \Illuminate\Support\Collection
     */
    public function columns(array $rows)
    {
        $columns = new Collection();

        foreach ($rows as $row) {
            $columns->push($this->column($row));
        }

        return $columns;
    }

    /**
     * Create a Document object with the given row data.
     *
     * @param array $row
     *
     * @return \Blixt\Models\Document
     */
    public function document(array $row)
    {
        return new Document($row['id'], $row['key']);
    }

    /**
     * Create a collection of Document objects with the given set of rows.
     *
     * @param array $rows
     *
     * @return \Illuminate\Support\Collection
     */
    public function documents(array $rows)
    {
        $documents = new Collection();

        foreach ($rows as $row) {
            $documents->push($this->document($row));
        }

        return $documents;
    }

    /**
     * Create a Field object with the given row data.
     *
     * @param array $row
     *
     * @return \Blixt\Models\Field
     */
    public function field(array $row)
    {
        return new Field($row['id'], $row['document_id'], $row['column_id'], $row['value']);
    }

    /**
     * Create a collection of Field objects with the given set of rows.
     *
     * @param array $rows
     *
     * @return \Illuminate\Support\Collection
     */
    public function fields(array $rows)
    {
        $fields = new Collection();

        foreach ($rows as $row) {
            $fields->push($this->field($row));
        }

        return $fields;
    }

    /**
     * Create a Word object with the given row data.
     *
     * @param array $row
     *
     * @return \Blixt\Models\Word
     */
    public function word(array $row)
    {
        return new Word($row['id'], $row['word']);
    }

    /**
     * Create a collection of Word objects with the given set of rows.
     *
     * @param array $rows
     *
     * @return \Illuminate\Support\Collection
     */
    public function words(array $rows)
    {
        $words = new Collection();

        foreach ($rows as $row) {
            $words->push($this->word($row));
        }

        return $words;
    }

    /**
     * Create a Presence object with the given row data.
     *
     * @param array $row
     *
     * @return \Blixt\Models\Presence
     */
    public function presence(array $row)
    {
        return new Presence($row['id'], $row['field_id'], $row['word_id'], $row['frequency']);
    }

    /**
     * Create a collection of Presence objects with the given set of rows.
     *
     * @param array $rows
     *
     * @return \Illuminate\Support\Collection
     */
    public function presences(array $rows)
    {
        $presences = new Collection();

        foreach ($rows as $row) {
            $presences->push($this->presence($row));
        }

        return $presences;
    }

    /**
     * Create an Occurrence object with the given row data.
     *
     * @param array $row
     *
     * @return \Blixt\Models\Occurrence
     */
    public function occurrence(array $row)
    {
        return new Occurrence($row['id'], $row['presence_id'], $row['position']);
    }

    /**
     * Create a collection of Occurrence objects with the given set of rows.
     *
     * @param array $rows
     *
     * @return \Illuminate\Support\Collection
     */
    public function occurrences(array $rows)
    {
        $occurrences = new Collection();

        foreach ($rows as $row) {
            $occurrences->push($this->occurrence($row));
        }

        return $occurrences;
    }
}