<?php

namespace Blixt\Storage\Repositories;

use Blixt\Storage\Entities\Schema;

interface SchemaRepository
{
    /**
     * @param string $name
     *
     * @return \Blixt\Storage\Entities\Schema|null
     */
    public function findByName($name);

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Storage\Entities\Schema
     */
    public function save(Schema $schema);
}