<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToField;
use Blixt\Persistence\Entities\Concerns\BelongsToTerm;

class Occurrence extends Entity
{
    use BelongsToField, BelongsToTerm;

    const TABLE = 'occurrences';
    const FIELD_FIELD_ID = 'field_id';
    const FIELD_TERM_ID = 'term_id';
    const FIELD_FREQUENCY = 'frequency';

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
     * Convert this entity to an array for storage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            static::FIELD_ID => $this->getId(),
            static::FIELD_FIELD_ID => $this->getFieldId(),
            static::FIELD_TERM_ID => $this->getTermId(),
            static::FIELD_FREQUENCY => $this->getFrequency(),
        ];
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

    /**
     * Create an Occurrence from the given array.
     *
     * @param array $array
     *
     * @return \Blixt\Persistence\Entities\Occurrence
     */
    public static function fromArray(array $array): Occurrence
    {
        return new static(
            $array[static::FIELD_ID],
            $array[static::FIELD_FIELD_ID],
            $array[static::FIELD_TERM_ID],
            $array[static::FIELD_FREQUENCY]
        );
    }
}