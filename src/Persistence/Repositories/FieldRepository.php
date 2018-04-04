<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Field;

class FieldRepository extends Repository
{
    /**
     * Save the given field, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Field $field
     *
     * @return \Blixt\Persistence\Entities\Field
     */
    public function save(Field $field): Field
    {
        return $field->exists() ? $this->driver()->update($field) : $this->driver()->insert($field);
    }
}