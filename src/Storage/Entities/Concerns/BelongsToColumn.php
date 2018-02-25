<?php

namespace Blixt\Storage\Entities\Concerns;

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
        $this->columnId = $columnId !== null ? intval($columnId) : null;
    }

    /**
     * Fluent getter/setter for columnId.
     *
     * @param int|null|mixed $columnId
     *
     * @return $this|int|null
     */
    public function columnId($columnId = null)
    {
        if (func_num_args() === 0) {
            return $this->getColumnId();
        }

        $this->setColumnId($columnId);

        return $this;
    }
}