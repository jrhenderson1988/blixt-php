<?php

namespace Blixt\Storage\Repositories;

interface FieldRepository
{
    /**
     * @param int|mixed         $documentId
     * @param int|mixed         $columnId
     * @param string|mixed|null $value
     *
     * @return \Blixt\Storage\Entities\Field
     */
    public function create($documentId, $columnId, $value = null);
}