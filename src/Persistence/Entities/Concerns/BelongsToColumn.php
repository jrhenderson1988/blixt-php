<?php

namespace Blixt\Persistence\Entities\Concerns;

use Blixt\Persistence\Entities\Column;

trait BelongsToColumn
{
    /**
     * @var int
     */
    protected $columnId;

    /**
     * @return int
     */
    public function getColumnId(): int
    {
        return $this->columnId;
    }

    /**
     * @param int $columnId
     */
    public function setColumnId(int $columnId): void
    {
        $this->columnId = $columnId;
    }

    /**
     * @param \Blixt\Persistence\Entities\Column $column
     */
    public function setColumnIdFromColumn(Column $column): void
    {
        $this->columnId = $column->getId();
    }
}
