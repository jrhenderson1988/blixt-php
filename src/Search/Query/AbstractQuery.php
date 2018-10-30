<?php

namespace Blixt\Search\Query;

use Blixt\Search\Query\Clause\Clause;
use Illuminate\Support\Collection;

abstract class AbstractQuery implements Query
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $clauses;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int|null
     */
    protected $chunkSize;

    /**
     * AbstractQuery constructor.
     *
     * @param \Illuminate\Support\Collection $clauses
     * @param int $page
     * @param int|null $limit
     * @param int|null $chunkSize
     */
    public function __construct(Collection $clauses, int $page = 1, ?int $limit = null, ?int $chunkSize = null)
    {
        $this->setClauses($clauses);
        $this->page = $page;
        $this->limit = $limit;
        $this->chunkSize = $chunkSize;
    }

    /**
     * Get the collection of clauses that form the query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClauses(): Collection
    {
        return $this->clauses;
    }

    /**
     * Set the clauses for this query.
     *
     * @param \Illuminate\Support\Collection $clauses
     */
    public function setClauses(Collection $clauses): void
    {
        $this->clauses = $clauses->map(function (Clause $clause) {
            return $clause;
        })->keyBy(function (Clause $clause) {
            return $clause->getValue();
        });
    }

    /**
     * Get the total number of results desired per page.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Get the page for which we're getting results.
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * The developer may wish to change the default number of documents that are evaluated at any one time for different
     * queries. This method can be used to serve that purpose.
     *
     * @return int|null
     */
    public function getChunkSize(): ?int
    {
        return $this->chunkSize;
    }
}
