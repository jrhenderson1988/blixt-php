<?php

namespace Blixt\Persistence\Entities;

use Blixt\Persistence\Entities\Concerns\BelongsToSchema;

class Document extends Entity
{
    use BelongsToSchema;

    const TABLE = 'documents';
    const FIELD_SCHEMA_ID = 'schema_id';
    const FIELD_KEY = 'key';

    /**
     * @var mixed
     */
    protected $key;

    /**
     * Document constructor.
     *
     * @param int|null $id
     * @param int      $schemaId
     * @param mixed    $key
     */
    public function __construct(?int $id, int $schemaId, $key)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setKey($key);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
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
            static::FIELD_KEY => $this->getKey()
        ];
    }

    /**
     * Factory method to create a new Document.
     *
     * @param int   $schemaId
     * @param mixed $key
     *
     * @return \Blixt\Persistence\Entities\Document
     */
    public static function create(int $schemaId, $key): Document
    {
        return new static(null, $schemaId, $key);
    }

    /**
     * Factory method to create a new Document from an existing record.
     *
     * @param int   $id
     * @param int   $schemaId
     * @param mixed $key
     *
     * @return \Blixt\Persistence\Entities\Document
     */
    public static function make(int $id, int $schemaId, $key): Document
    {
        return new static($id, $schemaId, $key);
    }

    /**
     * Create an entity from the given array.
     *
     * @param array $array
     *
     * @return \Blixt\Persistence\Entities\Document
     */
    public static function fromArray(array $array): Document
    {
        return new static(
            $array[static::FIELD_ID],
            $array[static::FIELD_SCHEMA_ID],
            $array[static::FIELD_KEY]
        );
    }
}