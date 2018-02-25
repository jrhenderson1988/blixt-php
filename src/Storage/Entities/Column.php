<?php

namespace Blixt\Storage\Entities;

use Blixt\Storage\Entities\Concerns\BelongsToSchema;

class Column extends Entity
{
    use BelongsToSchema;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var bool|null
     */
    protected $isIndexed;

    /**
     * @var bool|null
     */
    protected $isStored;

    /**
     * Column constructor.
     *
     * @param int|null|mixed    $id
     * @param int|null|mixed    $schemaId
     * @param string|null|mixed $name
     * @param bool|null|mixed   $isIndexed
     * @param bool|null|mixed   $isStored
     */
    public function __construct($id = null, $schemaId = null, $name = null, $isIndexed = null, $isStored = null)
    {
        parent::__construct($id);

        $this->setSchemaId($schemaId);
        $this->setName($name);
        $this->setIndexed($isIndexed);
        $this->setStored($isStored);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null|mixed $name
     */
    public function setName($name)
    {
        $this->name = $name !== null ? strval($name) : null;
    }

    /**
     * Fluent getter/setter for name.
     *
     * @param string|null|mixed $name
     *
     * @return $this|null|string
     */
    public function name($name = null)
    {
        if (func_num_args() === 0) {
            return $this->getName();
        }

        $this->setName($name);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isIndexed()
    {
        return $this->isIndexed;
    }

    /**
     * @param bool|null|mixed $isIndexed
     */
    public function setIndexed($isIndexed)
    {
        $this->isIndexed = $isIndexed !== null ? !! $isIndexed : null;
    }

    /**
     * Fluent getter/setter for isIndexed.
     *
     * @param bool|null|mixed $indexed
     *
     * @return $this|bool|null
     */
    public function indexed($indexed = null)
    {
        if (func_num_args() === 0) {
            return $this->isIndexed();
        }

        $this->setIndexed($indexed);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isStored()
    {
        return $this->isStored;
    }

    /**
     * @param bool|null|mixed $isStored
     */
    public function setStored($isStored)
    {
        $this->isStored = $isStored !== null ? !! $isStored : null;
    }

    /**
     * Fluent getter/setter for isStored.
     *
     * @param bool|null|mixed $stored
     *
     * @return $this|bool|null
     */
    public function stored($stored = null)
    {
        if (func_num_args() === 0) {
            return $this->isStored();
        }

        $this->setStored($stored);

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
            'setName' => ['name', 'setName'],
            'setIndexed' => ['indexed', 'is_indexed', 'isIndexed', 'setIndexed'],
            'setStored' => ['stored', 'is_stored', 'isStored', 'setStored']
        ]);
    }
}