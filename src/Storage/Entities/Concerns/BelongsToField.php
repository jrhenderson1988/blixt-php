<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Field;

trait BelongsToField
{
    /**
     * @var int
     */
    protected $fieldId;

    /**
     * @return int
     */
    public function getFieldId(): int
    {
        return $this->fieldId;
    }

    /**
     * @param int $fieldId
     */
    public function setFieldId(int $fieldId): void
    {
        $this->fieldId = $fieldId;
    }

    /**
     * @param \Blixt\Storage\Entities\Field $field
     */
    public function setFieldIdFromField(Field $field): void
    {
        $this->fieldId = $field->getId();
    }
}