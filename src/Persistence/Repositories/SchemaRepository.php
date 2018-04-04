<?php

namespace Blixt\Persistence\Repositories;

use Blixt\Persistence\Entities\Schema;

class SchemaRepository extends Repository
{
    /**
     * Find a schema by the given name.
     *
     * @param string $name
     *
     * @return \Blixt\Persistence\Entities\Schema|null
     */
    public function findByName(string $name): ?Schema
    {
        return $this->driver()->findBy(Schema::class, [Schema::FIELD_NAME => $name]);
    }

    /**
     * Save the given schema, updating it in the storage if it already exists, or inserting a new one if it doesn't.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     *
     * @return \Blixt\Persistence\Entities\Schema
     */
    public function save(Schema $schema): Schema
    {
        return $schema->exists() ? $this->driver()->update($schema) : $this->driver()->insert($schema);
    }
}