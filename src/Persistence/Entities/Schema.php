<?php

namespace Blixt\Persistence\Entities;

use Illuminate\Support\Collection;

class Schema extends Entity
{
    const TABLE = 'schemas';
    const FIELD_NAME = 'name';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * Schema constructor.
     *
     * @param int|null $id
     * @param string   $name
     */
    public function __construct(?int $id, string $name)
    {
        parent::__construct($id);

        $this->setName($name);
        $this->setColumns(new Collection());
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
     * @param \Illuminate\Support\Collection $columns
     */
    public function setColumns(Collection $columns): void
    {
        $this->columns = $columns->filter(function (Column $column) {
            return $column->getSchemaId() === $this->getId();
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getColumns(): Collection
    {
        return $this->columns;
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
            static::FIELD_NAME => $this->getName()
        ];
    }

    /**
     * Factory method to create a new Schema.
     *
     * @param string $name
     *
     * @return \Blixt\Persistence\Entities\Schema
     */
    public static function create(string $name): Schema
    {
        return new static(null, $name);
    }

    /**
     * Factory method to make a Schema from an existing record.
     *
     * @param int    $id
     * @param string $name
     *
     * @return \Blixt\Persistence\Entities\Schema
     */
    public static function make(int $id, string $name): Schema
    {
        return new static($id, $name);
    }

    /**
     * @param array $array
     *
     * @return \Blixt\Persistence\Entities\Schema
     */
    public static function fromArray(array $array): Entity
    {
        return new static(
            $array[static::FIELD_ID],
            $array[static::FIELD_NAME]
        );
    }
}