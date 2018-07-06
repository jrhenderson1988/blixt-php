<?php

namespace Blixt\Persistence\Entities\Concerns;

use Blixt\Persistence\Entities\Field;

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
     * @param \Blixt\Persistence\Entities\Field $field
     */
    public function setFieldIdFromField(Field $field): void
    {
        $this->fieldId = $field->getId();
    }
}
