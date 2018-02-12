<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Schema;
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
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Blixt\Storage\Entities\Word   $word
     * @param int|mixed                      $fieldCount
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function create(Schema $schema, Word $word, $fieldCount = 0);
}