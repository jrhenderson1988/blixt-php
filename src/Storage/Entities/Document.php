<?php

namespace Blixt\Storage\Entities;

use Blixt\Storage\Entities\Concerns\BelongsToSchema;

class Document extends Entity
{
    use BelongsToSchema;

    /**
     * @var null|mixed
     */
    protected $key;

    /**
     * Document constructor.
     *
     * @param int|null|mixed $id
     * @param int|null|mixed $schemaId
     * @param null|mixed     $key
     */
    public function __construct($id = null, $schemaId = null, $key = null)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setKey($key);
    }

    /**
     * @return null|mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param null|mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Fluent getter/setter for key.
     *
     * @param null|mixed $key
     *
     * @return $this|mixed|null
     */
    public function key($key = null)
    {
        if (func_num_args() === 0) {
            return $this->getKey();
        }

        $this->setKey($key);

        return $this;
    }

    /**
     * Create a new document from the set of attributes given.
     *
     * @param array|object $attributes
     *
     * @return \Blixt\Storage\Entities\Document
     */
    public static function make($attributes)
    {
        $document = new static();

        foreach ((array) $attributes as $key => $value) {
            if (in_array($key, ['id', 'setId'])) {
                $document->setId($value);
            } elseif (in_array($key, ['schema_id', 'schemaId', 'setSchemaId'])) {
                $document->setSchemaId($value);
            } elseif (in_array($key, ['key', 'setKey'])) {
                $document->setKey($value);
            }
        }

        return $document;
    }
}