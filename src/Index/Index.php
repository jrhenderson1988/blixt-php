<?php

namespace Blixt\Index;

use Blixt\Documents\Document;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\StemmerInterface as Stemmer;
use Blixt\Storage\FactoryInterface as StorageFactory;
use Blixt\Tokenization\TokenizerInterface as Tokenizer;
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
     * @param string                                 $name
     * @param \Blixt\Storage\FactoryInterface        $connector
     * @param \Blixt\Stemming\StemmerInterface       $stemmer
     * @param \Blixt\Tokenization\TokenizerInterface $tokenizer
     *
     * @throws \Exception
     */
    public function __construct($name, StorageFactory $connector, Stemmer $stemmer, Tokenizer $tokenizer)
    {
        $this->name = $name;
        $this->storage = $connector->create($name);
    }

    /**
     * Tel if this index exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->storage->exists();
    }

    /**
     * Create this index with the given column definition.
     *
     * @param \Blixt\Index\Schema\Schema $schema
     *
     * @return bool
     * @throws \Exception
     */
    public function create(Schema $schema)
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->create($schema);
            $this->storage->commitTransaction();

            return true;
        } catch (Exception $ex) {
            $this->storage->rollBackTransaction();

            throw $ex;
        }
    }

    /**
     * Destroy the index.
     *
     * @return bool
     */
    public function destroy()
    {
        if ($this->exists()) {
            return $this->storage->destroy();
        }

        return false;
    }

    public function addDocument(Document $document)
    {

    }

    public function addDocuments(Collection $documents)
    {

    }

    public function search()
    {

    }
}