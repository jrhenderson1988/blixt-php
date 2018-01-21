<?php

namespace Blixt;

use Blixt\Exceptions\IndexDoesNotExistException;
use Blixt\Index\Index;
use Blixt\Index\Schema\Blueprint;
use Blixt\Stemming\EnglishStemmer;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Storage;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;

// TODO - Blixt class now represents the parent index, the index class now represents a sub-index based around a schema

class Blixt
{
    /**
     * @var \Blixt\Storage\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $schemas;

    /**
     * Blixt constructor.
     *
     * @param \Blixt\Storage\Storage             $storage
     * @param \Blixt\Stemming\Stemmer|null       $stemmer
     * @param \Blixt\Tokenization\Tokenizer|null $tokenizer
     */
    public function __construct(Storage $storage, Stemmer $stemmer = null, Tokenizer $tokenizer = null)
    {
        $this->storage = $storage;
        $this->stemmer = $stemmer instanceof Stemmer ? $stemmer : new EnglishStemmer();
        $this->tokenizer = $tokenizer instanceof Tokenizer ? $tokenizer : new DefaultTokenizer();

        $this->loadSchemas();
    }

    /**
     * Install Blixt into the storage engine. This effectively creates the underlying storage schema if it does not
     * already exist.
     *
     * @param \Blixt\Storage\Storage $storage
     *
     * @return bool
     */
    public static function install(Storage $storage)
    {
        if (! $storage->exists()) {
            return $storage->create();
        }

        return true;
    }

    /**
     * @return \Blixt\Storage\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Get the stemmer.
     *
     * @return \Blixt\Stemming\Stemmer
     */
    public function getStemmer()
    {
        return $this->stemmer;
    }

    /**
     * Get the tokenizer.
     *
     * @return \Blixt\Tokenization\Tokenizer
     */
    public function getTokenizer()
    {
        return $this->tokenizer;
    }

    /**
     * Load all of the schemas from the storage, with their associated columns.
     */
    protected function loadSchemas()
    {
        $columns = $this->getStorage()->columns()->all();

        // Note: The Schema::setColumns method filters out columns that do not belong to it.
        $this->schemas = $this->getStorage()->schemas()->all()->map(function (Schema $schema) use ($columns) {
            $schema->setColumns($columns);
        });
    }

    /**
     * Open an index for the given schema.
     *
     * @param string|int|mixed $schema
     *
     * @return \Blixt\Index\Index
     * @throws \Blixt\Exceptions\IndexDoesNotExistException
     */
    public function open($schema = null)
    {
        $schema = $this->findSchema($schema);

        if (! $schema) {
            throw new IndexDoesNotExistException(
                'The requested schema does not exist.'
            );
        }

        return $this->createIndexForSchema($schema);
    }

    public function create($blueprint, $closure = null)
    {
        // TODO
        // - This method can be called in two ways, by either providing a blueprint directly, or by providing a name and
        //   a closure that is responsible for building the blueprint.
        // - Firstly, build a blueprint if the latter method is used.
        // - Look up the name of the blueprint in our schemas, throwing an exception if we find an existing schema
        // - Use the storage to create a schema object using the blueprint name
        // - Use the storage to create a set of columns based upon the blueprint's columns
        // - Ensure that the columns created are mapped into the given schema and that the schema is then added to the
        //   schemas property.
        // - Create an Index object for that schema
        // - Cache the Index object and return it.
    }

    /**
     * Find the schema identified by the ID or name provided.
     *
     * @param string|int|mixed $identifier
     *
     * @return \Blixt\Storage\Entities\Schema|null
     */
    protected function findSchema($identifier)
    {
        if (is_int($identifier) || ctype_digit($identifier)) {
            $schema = $this->schemas->first(function (Schema $schema) use ($identifier) {
                return $schema->getId() == $identifier;
            });

            if ($schema) {
                return $schema;
            }
        }

        // find by name
        return $this->schemas->first(function (Schema $schema) use ($identifier) {
            return $schema->getName() == $identifier;
        });
    }

    /**
     * Create an Index object for the given schema.
     *
     * @param \Blixt\Storage\Entities\Schema $schema
     *
     * @return \Blixt\Index\Index
     */
    protected function createIndexForSchema(Schema $schema)
    {
        return new Index($this, $schema);
    }
}