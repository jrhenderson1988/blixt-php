<?php

namespace Blixt;

use Blixt\Documents\Document;
use Blixt\Exceptions\UndefinedSchemaException;
use Blixt\Models\Schema;
use Blixt\Storage\FactoryInterface as StorageFactory;
use Exception;
use Illuminate\Support\Collection;

class Index
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Blixt\Storage\EngineInterface
     */
    protected $storage;

    /**
     * Index constructor.
     *
     * @param string                          $name
     * @param \Blixt\Storage\FactoryInterface $connector
     *
     * @throws \Exception
     */
    public function __construct($name, StorageFactory $connector)
    {
        $this->name = $name;
        $this->storage = $connector->create($name);

        if (!$this->storage->exists()) {
//            $this->storage->beginTransaction();

            try {
                $this->storage->create();
//                $this->storage->commitTransaction();
            } catch (Exception $ex) {
//                $this->storage->rollBackTransaction();

                throw $ex;
            }
        }
    }

    public function addDocument($schemaName, Document $document)
    {
        $schema = $this->storage->findSchemaByName($schemaName);

        if (!$schema || !$schema instanceof Schema) {
            throw new UndefinedSchemaException(
                "The schema '{$schemaName}' is not defined, please create it first."
            );
        }

        // TODO - Logic for adding a document, using the storage engine methods.
    }

    public function addDocuments($schema, Collection $documents)
    {

    }

    public function search()
    {

    }

    public function destroy()
    {
        return $this->storage->destroy();
    }
}