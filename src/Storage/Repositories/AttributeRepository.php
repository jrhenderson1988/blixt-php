<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Attribute;
use Blixt\Storage\Entities\Schema;
use Illuminate\Support\Collection;

interface AttributeRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function all();

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Illuminate\Support\Collection
     */
    public function allForSchema(Schema $schema);

    /**
     * @param \Illuminate\Support\Collection $schemas
     *
     * @return \Illuminate\Support\Collection
     */
    public function allForSchemas(Collection $schemas);

    /**
     * @param int|mixed $id
     *
     * @return \Blixt\Storage\Entities\Attribute
     */
    public function find($id);

    /**
     * @param string|mixed $name
     *
     * @return \Blixt\Storage\Entities\Attribute
     */
    public function findByName($name);

    /**
     * @param \Blixt\Storage\Entities\Attribute $attribute
     *
     * @return \Blixt\Storage\Entities\Attribute
     */
    public function save(Attribute $attribute);
}