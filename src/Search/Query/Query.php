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

    /**
     * Get the total number of results desired per page.
     *
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * Get the page for which we're getting results.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * The developer may wish to change the default number of documents that are evaluated at any one time for different
     * queries. This method can be used to serve that purpose.
     *
     * @return int|null
     */
    public function getChunkSize(): ?int;
}
