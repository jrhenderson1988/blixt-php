<?php

namespace Blixt\Storage\Repositories;

interface ColumnRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();
}