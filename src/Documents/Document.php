<?php

namespace Blixt\Documents;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Document
{
    /**
     * @var mixed
     */
    protected $key;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $fields;

    /**
     * Document constructor.
     *
     * @param mixed $key
     * @param \Illuminate\Support\Collection|array|\Blixt\Documents\Field|null $fields
     */
    public function __construct($key, $fields = null)
    {
        $this->setKey($key);
        $this->setFields(is_null($fields) ? [] : $fields);
    }

    /**
     * Set the key for the document.
     *
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get the key for the document.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Add a field to the fields collection.
     *
     * @param \Blixt\Documents\Field $field
     */
    public function addField(Field $field)
    {
        $this->fields->push($field);
    }

    /**
     * Set the fields for the document.
     *
     * @param \Illuminate\Support\Collection|\Blixt\Documents\Field|array $fields
     */
    public function setFields($fields)
    {
        $this->fields = new Collection();

        if (!$fields instanceof Collection && !is_array($fields) && !$fields instanceof Field) {
            throw new InvalidArgumentException(
                'Fields must be a Collection, an array or a single Field.'
            );
        }

        foreach (new Collection($fields instanceof Field ? [$fields] : $fields) as $field) {
            $this->addField($field);
        }
    }

    /**
     * Get the document's fields.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFields()
    {
        return $this->fields;
    }
}