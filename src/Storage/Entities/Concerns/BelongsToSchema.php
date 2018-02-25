<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Schema;

trait BelongsToSchema
{
    /**
     * @var int|null
     */
    protected $schemaId;

    /**
     * @return int|null
     */
    public function getSchemaId()
    {
        return $this->schemaId;
    }

    /**
     * @param int|null|mixed $schemaId
     */
    public function setSchemaId($schemaId)
    {
        $this->schemaId = $schemaId !== null
            ? ($schemaId instanceof Schema ? $schemaId->getId() : intval($schemaId))
            : null;
    }

    /**
     * Fluent getter/setter for schemaId.
     *
     * @param int|null|mixed $schemaId
     *
     * @return $this|int|null
     */
    public function schemaId($schemaId = null)
    {
        if (func_num_args() === 0) {
            return $this->getSchemaId();
        }

        $this->setSchemaId($schemaId);

        return $this;
    }
}