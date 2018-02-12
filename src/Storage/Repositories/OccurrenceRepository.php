<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Field;
use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Entities\Term;

interface OccurrenceRepository
{
    /**
     * @param \Blixt\Storage\Entities\Field $field
     * @param \Blixt\Storage\Entities\Term  $term
     * @param int                           $frequency
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function create(Field $field, Term $term, $frequency);

    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public function save(Occurrence $occurrence);
}