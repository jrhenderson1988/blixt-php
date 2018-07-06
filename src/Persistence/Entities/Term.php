<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToSchema;
use Blixt\Persistence\Entities\Concerns\BelongsToWord;

class Term extends Entity
{
    use BelongsToSchema, BelongsToWord;

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
}
