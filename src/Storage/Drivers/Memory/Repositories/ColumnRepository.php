<?php

namespace Blixt\Storage\Drivers\Memory\Repositories;

use Blixt\Storage\Drivers\Memory\Entities\Column;
use Blixt\Storage\Repositories\ColumnRepository as ColumnRepositoryInterface;
use Illuminate\Support\Collection;

class ColumnRepository extends AbstractRepository implements ColumnRepositoryInterface
{
    /**
     * @param array $row
     *
     * @return \Blixt\Storage\Drivers\Memory\Entities\Column
     */
    protected function map(array $row)
    {
        return new Column($row['id'], $row['schema_id'], $row['name'], $row['indexed'], $row['stored']);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        $results = new Collection();

        foreach ($this->storage->all() as $key => $row) {
            $results->put($key, $this->map(array_merge(['id' => $key], $row)));
        }

        return $results;
    }
}