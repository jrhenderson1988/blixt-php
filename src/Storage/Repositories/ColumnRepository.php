<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Illuminate\Support\Collection;

interface ColumnRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Illuminate\Support\Collection
     */
    public function allForSchema(Schema $schema);

    /**
     * @param \Illuminate\Support\Collection $schemas
     *
     * @return \Illuminate\Support\Collection
     */
    public function allForSchemas(Collection $schemas);

    /**
     * @param int|mixed $id
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function find($id);

    /**
     * @param string|mixed $name
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function findByName($name);

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function save(Column $column);
}