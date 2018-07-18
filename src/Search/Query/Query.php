<?php

namespace Blixt\Search\Query;

use Illuminate\Support\Collection;

interface Query
{
    /**
     * Get the collection of clauses that form the query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClauses(): Collection;
}
