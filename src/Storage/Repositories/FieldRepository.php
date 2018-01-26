<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Document;

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
}