<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Field;

interface FieldRepository
{
    /**
     * @param \Blixt\Storage\Entities\Document $document
     * @param int|mixed                        $columnId
     * @param string|mixed|null                $value
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function create(Document $document, $columnId, $value = null);

    /**
     * @param \Blixt\Storage\Entities\Field $field
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function save(Field $field);
}