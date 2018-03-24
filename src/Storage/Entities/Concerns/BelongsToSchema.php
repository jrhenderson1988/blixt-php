<?php

namespace Blixt\Storage\Entities\Concerns;

use Blixt\Storage\Entities\Schema;

trait BelongsToSchema
{
    /**
     * @var int
     */
    protected $schemaId;

    /**
     * @return int
     */
    public function getSchemaId(): int
    {
        return $this->schemaId;
    }

    /**
     * @param int $schemaId
     */
    public function setSchemaId(int $schemaId): void
    {
        $this->schemaId = $schemaId;
    }

    /**
     * @param \Blixt\Storage\Entities\Schema $schema
     */
    public function setSchemaIdFromSchema(Schema $schema): void
    {
        $this->schemaId = $schema->getId();
    }
}