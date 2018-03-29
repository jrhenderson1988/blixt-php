<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToSchema;
use Blixt\Persistence\Entities\Concerns\BelongsToWord;

class Term extends Entity
{
    use BelongsToSchema, BelongsToWord;

    const TABLE = 'term';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_WORD_ID = 'word_id';
    const FIELD_FIELD_COUNT = 'field_count';

    /**
     * @var int
     */
    protected $fieldCount;

    /**
     * Term constructor.
     *
     * @param int|null $id
     * @param int      $schemaId
     * @param int      $wordId
     * @param int      $fieldCount
     */
    public function __construct(?int $id, int $schemaId, int $wordId, int $fieldCount)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setWordId($wordId);
        $this->setFieldCount($fieldCount);
    }

    /**
     * @return int
     */
    public function getFieldCount(): int
    {
        return $this->fieldCount;
    }

    /**
     * @param int $fieldCount
     */
    public function setFieldCount(int $fieldCount): void
    {
        $this->fieldCount = $fieldCount;
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
            static::FIELD_SCHEMA_ID => $this->getSchemaId(),
            static::FIELD_WORD_ID => $this->getWordId(),
            static::FIELD_FIELD_COUNT => $this->getFieldCount()
        ];
    }

    /**
     * Factory method to create a new Term.
     *
     * @param int $schemaId
     * @param int $wordId
     * @param int $fieldCount
     *
     * @return \Blixt\Persistence\Entities\Term
     */
    public static function create(int $schemaId, int $wordId, int $fieldCount): Term
    {
        return new static(null, $schemaId, $wordId, $fieldCount);
    }

    /**
     * Factory method to make a Term from an existing record.
     *
     * @param int $id
     * @param int $schemaId
     * @param int $wordId
     * @param int $fieldCount
     *
     * @return \Blixt\Persistence\Entities\Term
     */
    public static function make(int $id, int $schemaId, int $wordId, int $fieldCount): Term
    {
        return new static($id, $schemaId, $wordId, $fieldCount);
    }

    /**
     * Create a Term from the given array.
     *
     * @param array $array
     *
     * @return \Blixt\Persistence\Entities\Term
     */
    public static function fromArray(array $array): Term
    {
        return new static(
            $array[static::FIELD_ID],
            $array[static::FIELD_SCHEMA_ID],
            $array[static::FIELD_WORD_ID],
            $array[static::FIELD_FIELD_COUNT]
        );
    }
}