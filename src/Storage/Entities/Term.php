<?php

namespace Blixt\Storage\Entities;

use Blixt\Storage\Entities\Concerns\BelongsToSchema;
use Blixt\Storage\Entities\Concerns\BelongsToWord;

class Term extends Entity
{
    use BelongsToSchema, BelongsToWord;

    /**
     * @var int|null
     */
    protected $fieldCount;

    /**
     * Term constructor.
     *
     * @param int|null|mixed $id
     * @param int|null|mixed $schemaId
     * @param int|null|mixed $wordId
     * @param int|null|mixed $fieldCount
     */
    public function __construct($id = null, $schemaId = null, $wordId = null, $fieldCount = null)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setWordId($wordId);
        $this->setFieldCount($fieldCount);
    }

    /**
     * @return int|null|mixed
     */
    public function getFieldCount()
    {
        return $this->fieldCount;
    }

    /**
     * @param int|null|mixed $fieldCount
     */
    public function setFieldCount($fieldCount)
    {
        $this->fieldCount = $fieldCount !== null ? intval($fieldCount) : null;
    }

    /**
     * Fluent getter/setter for fieldCount.
     *
     * @param int|null|mixed $fieldCount
     *
     * @return $this|int|mixed|null
     */
    public function fieldCount($fieldCount = null)
    {
        if (func_num_args() === 0) {
            return $this->getFieldCount();
        }

        $this->setFieldCount($fieldCount);

        return $this;
    }
}