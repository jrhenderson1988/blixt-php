<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Column;

trait BelongsToColumn
{
    /**
     * @var int|null
     */
    protected $columnId;

    /**
     * @return int|null
     */
    public function getColumnId()
    {
        return $this->columnId;
    }

    /**
     * @param int|null|mixed $columnId
     */
    public function setColumnId($columnId)
    {
        $this->columnId = $columnId !== null
            ? ($columnId instanceof Column ? $columnId->getId() : intval($columnId))
            : null;
    }
}