<?php

namespace Blixt\Persistence\Entities\Concerns;

use Blixt\Persistence\Entities\Schema;

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
     * @param \Blixt\Persistence\Entities\Schema $schema
     */
    public function setSchemaIdFromSchema(Schema $schema): void
    {
        $this->schemaId = $schema->getId();
    }
}
