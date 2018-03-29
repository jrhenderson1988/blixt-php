<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToSchema;

class Column extends Entity
{
    use BelongsToSchema;

    const TABLE = 'columns';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_NAME = 'name';
    const FIELD_IS_INDEXED = 'is_indexed';
    const FIELD_IS_STORED = 'is_stored';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isIndexed;

    /**
     * @var bool
     */
    protected $isStored;

    /**
     * Column constructor.
     *
     * @param int|null $id
     * @param int      $schemaId
     * @param string   $name
     * @param bool     $isIndexed
     * @param bool     $isStored
     */
    public function __construct(?int $id, int $schemaId, string $name, bool $isIndexed, bool $isStored)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setName($name);
        $this->setIndexed($isIndexed);
        $this->setStored($isStored);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isIndexed(): bool
    {
        return $this->isIndexed;
    }

    /**
     * @param bool $isIndexed
     */
    public function setIndexed(bool $isIndexed): void
    {
        $this->isIndexed = $isIndexed;
    }

    /**
     * @return bool
     */
    public function isStored(): bool
    {
        return $this->isStored;
    }

    /**
     * @param bool $isStored
     */
    public function setStored(bool $isStored): void
    {
        $this->isStored = $isStored;
    }

    /**
     * Get an array representation of the entity, for storage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            static::FIELD_ID => $this->getId(),
            static::FIELD_SCHEMA_ID => $this->getSchemaId(),
            static::FIELD_NAME => $this->getName(),
            static::FIELD_IS_INDEXED => $this->isIndexed(),
            static::FIELD_IS_STORED => $this->isStored()
        ];
    }

    /**
     * Factory method to create a new Column.
     *
     * @param int    $schemaId
     * @param string $name
     * @param bool   $isIndexed
     * @param bool   $isStored
     *
     * @return \Blixt\Persistence\Entities\Column
     */
    public static function create(int $schemaId, string $name, bool $isIndexed, bool $isStored): Column
    {
        return new static(null, $schemaId, $name, $isIndexed, $isStored);
    }

    /**
     * Factory method to make a Column from an existing record.
     *
     * @param int    $id
     * @param int    $schemaId
     * @param string $name
     * @param bool   $isIndexed
     * @param bool   $isStored
     *
     * @return \Blixt\Persistence\Entities\Column
     */
    public static function make(int $id, int $schemaId, string $name, bool $isIndexed, bool $isStored): Column
    {
        return new static($id, $schemaId, $name, $isIndexed, $isStored);
    }

    /**
     * Create a Column object from the given array.
     *
     * @param array $attributes
     *
     * @return \Blixt\Persistence\Entities\Column
     */
    public static function fromArray(array $attributes): Column
    {
        return new static(
            $attributes[static::FIELD_ID],
            $attributes[static::FIELD_SCHEMA_ID],
            $attributes[static::FIELD_NAME],
            $attributes[static::FIELD_IS_INDEXED],
            $attributes[static::FIELD_IS_STORED]
        );
    }
}