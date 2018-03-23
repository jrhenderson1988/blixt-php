<?php

namespace Blixt\Document;

use Illuminate\Support\Collection;

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
     * @param int                            $key
     * @param \Illuminate\Support\Collection $fields
     */
    public function __construct(int $key, Collection $fields = null)
    {
        $this->setKey($key);
        $this->setFields(! is_null($fields) ? $fields : new Collection());
    }

    /**
     * Set the key for the document.
     *
     * @param int $key
     */
    public function setKey(int $key)
    {
        $this->key = $key;
    }

    /**
     * Get the key for the document.
     *
     * @return int
     */
    public function getKey(): int
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
    public function getField(string $key)
    {
        return $this->fields->get($key);
    }

    /**
     * Add a field to the fields collection.
     *
     * @param string|mixed $key
     * @param mixed        $value
     */
    public function setField(string $key, $value): void
    {
        $this->fields->put($key, $value);
    }

    /**
     * Set the fields for the document.
     *
     * @param \Illuminate\Support\Collection|array $fields
     */
    public function setFields(Collection $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * Get the document's fields.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }
}