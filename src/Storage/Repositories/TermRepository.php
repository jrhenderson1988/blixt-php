<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Entities\Term;
use Blixt\Storage\Entities\Word;
use Illuminate\Support\Collection;

interface TermRepository
{
    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Blixt\Storage\Entities\Word   $word
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function findBySchemaAndWord(Schema $schema, Word $word);

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Illuminate\Support\Collection $words
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBySchemaAndWords(Schema $schema, Collection $words);

    /**
     * @param \Blixt\Storage\Entities\Term $term
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function save(Term $term);
}