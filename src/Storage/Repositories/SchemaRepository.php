<?php

namespace Blixt\Storage\Repositories;

interface SchemaRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();
}