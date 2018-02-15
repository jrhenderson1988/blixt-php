<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Column;

interface ColumnRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function save(Column $column);
}