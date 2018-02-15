<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Field;

interface FieldRepository
{
    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function save(Field $field);
}