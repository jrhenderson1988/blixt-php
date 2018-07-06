<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToField;
use Blixt\Persistence\Entities\Concerns\BelongsToTerm;

class Occurrence extends Entity
{
    use BelongsToField, BelongsToTerm;

    /**
     * @var int
     */
    protected $frequency;

    /**
     * Occurrence constructor.
     *
     * @param int|null $id
     * @param int      $fieldId
     * @param int      $termId
     * @param int      $frequency
     */
    public function __construct(?int $id, int $fieldId, int $termId, int $frequency)
    {
        parent::__construct($id);

        $this->setFieldId($fieldId);
        $this->setTermId($termId);
        $this->setFrequency($frequency);
    }

    /**
     * @return int
     */
    public function getFrequency(): int
    {
        return $this->frequency;
    }

    /**
     * @param int $frequency
     */
    public function setFrequency(int $frequency): void
    {
        $this->frequency = $frequency;
    }

    /**
     * Factory method to create a new Occurrence.
     *
     * @param int $fieldId
     * @param int $termId
     * @param int $frequency
     *
     * @return \Blixt\Persistence\Entities\Occurrence
     */
    public static function create(int $fieldId, int $termId, int $frequency): Occurrence
    {
        return new static(null, $fieldId, $termId, $frequency);
    }

    /**
     * Factory method to make a Field from an existing record.
     *
     * @param int $id
     * @param int $fieldId
     * @param int $termId
     * @param int $frequency
     *
     * @return \Blixt\Persistence\Entities\Occurrence
     */
    public static function make(int $id, int $fieldId, int $termId, int $frequency): Occurrence
    {
        return new static($id, $fieldId, $termId, $frequency);
    }
}
