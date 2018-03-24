<?php

namespace Blixt\Storage\Entities;

use Blixt\Storage\Entities\Concerns\BelongsToSchema;

class Document extends Entity
{
    use BelongsToSchema;

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
     * Factory method to create a new Document.
     *
     * @param int   $schemaId
     * @param mixed $key
     *
     * @return \Blixt\Storage\Entities\Document
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
     * @return \Blixt\Storage\Entities\Document
     */
    public static function make(int $id, int $schemaId, $key): Document
    {
        return new static($id, $schemaId, $key);
    }
}