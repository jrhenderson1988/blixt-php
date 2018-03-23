<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Illuminate\Support\Collection;

interface ColumnRepository
{
    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBySchema(Schema $schema): Collection;

    /**
     * @param \Blixt\Storage\Entities\Column $column
     *
     * @return \Blixt\Storage\Entities\Column
     */
    public function save(Column $column): Column;
}