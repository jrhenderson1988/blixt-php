<?php

namespace Blixt\Document;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class Indexable
{
    /**
     * @var int
     */
    protected $key;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $fields;

    /**
     * Document constructor.
     *
     * @param int|mixed $key
     * @param \Illuminate\Support\Collection|array|null $fields
     */
    public function __construct($key, $fields = null)
    {
        $this->setKey($key);
        $this->setFields(! is_null($fields) ? $fields : new Collection());
    }

    /**
     * Set the key for the document.
     *
     * @param int|mixed $key
     */
    public function setKey($key)
    {
        $this->key = intval($key);
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
     * Get a field's value by its key.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getField($key)
    {
        return $this->fields->get($key);
    }

    /**
     * Add a field to the fields collection.
     *
     * @param string|mixed $key
     * @param mixed        $value
     */
    public function setField($key, $value)
    {
        $this->fields->put($key, $value);
    }

    /**
     * Set the fields for the document.
     *
     * @param \Illuminate\Support\Collection|array $fields
     */
    public function setFields($fields)
    {
        if (! $fields instanceof Collection && ! is_array($fields)) {
            throw new InvalidArgumentException(
                'Fields must be a collection of an array.'
            );
        }

        $this->fields = new Collection();

        (new Collection($fields))->each(function ($item, $key) {
            $this->setField($key, $item);
        });
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