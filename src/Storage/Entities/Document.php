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
     * Mappings of the methods to sets of keys. That method will be used to set a property identified by one of the keys
     * when using the make method to create an instance of the entity.
     *
     * @return array
     */
    public static function getAttributeMappings()
    {
        return array_merge(parent::getAttributeMappings(), [
            'setSchemaId' => ['schema_id', 'schemaId', 'setSchemaId'],
            'setKey' => ['key', 'setKey']
        ]);
    }
}