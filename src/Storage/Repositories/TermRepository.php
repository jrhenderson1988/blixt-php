<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Entities\Word;

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
     * @param \Blixt\Storage\Entities\Word   $word
     * @param int|mixed                      $count
     *
     * @return \Blixt\Storage\Entities\Term
     */
    public function create(Schema $schema, Word $word, $count = 0);
}