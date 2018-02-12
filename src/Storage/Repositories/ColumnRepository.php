<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;

interface ColumnRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param string|mixed                   $name
     * @param bool|mixed                     $isIndexed
     * @param bool|mixed                     $isStored
     *
     * @return \Blixt\Storage\Entities\Column|null
     */
    public function create(Schema $schema, $name, $isIndexed, $isStored);

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function save(Column $column);
}