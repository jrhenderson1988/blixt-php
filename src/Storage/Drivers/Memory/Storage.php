<?php

namespace Blixt\Storage\Drivers\Memory;

use Blixt\Storage\Drivers\Memory\Repositories\ColumnRepository;
use Blixt\Storage\Drivers\Memory\Repositories\DocumentRepository;
use Blixt\Storage\Drivers\Memory\Repositories\FieldRepository;
use Blixt\Storage\Drivers\Memory\Repositories\OccurrenceRepository;
use Blixt\Storage\Drivers\Memory\Repositories\PositionRepository;
use Blixt\Storage\Drivers\Memory\Repositories\SchemaRepository;
use Blixt\Storage\Drivers\Memory\Repositories\TermRepository;
use Blixt\Storage\Drivers\Memory\Repositories\WordRepository;
use Blixt\Storage\Storage as StorageInterface;

class Storage implements StorageInterface
{
    /**
     * @var array
     */
    protected static $tables = [
        'columns',
        'documents',
        'fields',
        'occurrences',
        'positions',
        'schemas',
        'terms',
        'words'
    ];

    /**
     * @var array
     */
    protected $repositories;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $keys;

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        $this->repositories = [];
        $this->data = [];
        $this->keys = [];
    }

    /**
     * Tell if the storage represented by the engine exists.
     *
     * @return boolean
     */
    public function exists()
    {
        foreach (self::$tables as $table) {
            if (! isset($this->data[$table])) {
                return false;
            }
        }

        return true;
    }

    protected function insert($table, array $data)
    {
        $this->data[$key = $this->nextKey($table)] = $data;

        return $key;
    }

    public function update($table, $key, array $data)
    {
        $this->data[$table][$key] = array_merge($this->data[$table][$key], $data);

        return true;
    }

    public function find($table, $key)
    {
        return isset($this->data[$table][$key]) ? $this->data[$table][$key] : null;
    }

    public function all($table)
    {
        return $this->data[$table];
    }

    protected function nextKey($table)
    {
        return ++$this->keys[$table];
    }

    /**
     * Create the storage represented by the engine.
     *
     * @return bool
     */
    public function create()
    {
        foreach (self::$tables as $table) {
            $this->data[$table] = [];
            $this->keys[$table] = 0;
        }

        return true;
    }

    /**
     * Destroy the storage represented by the engine.
     *
     * @return boolean
     */
    public function destroy()
    {
        $this->data = [];
        $this->keys = [];

        return true;
    }

    /**
     * Get (or create) the column repository.
     *
     * @return \Blixt\Storage\Repositories\ColumnRepository
     */
    public function columns()
    {
        return $this->loadRepository(ColumnRepository::class);
    }

    /**
     * Get (or create) the document repository.
     *
     * @return \Blixt\Storage\Repositories\DocumentRepository
     */
    public function documents()
    {
        return $this->loadRepository(DocumentRepository::class);
    }

    /**
     * Get (or create) the field repository.
     *
     * @return \Blixt\Storage\Repositories\FieldRepository
     */
    public function fields()
    {
        return $this->loadRepository(FieldRepository::class);
    }

    /**
     * Get (or create) the occurrence repository.
     *
     * @return \Blixt\Storage\Repositories\OccurrenceRepository
     */
    public function occurrences()
    {
        return $this->loadRepository(OccurrenceRepository::class);
    }

    /**
     * Get (or create) the position repository.
     *
     * @return \Blixt\Storage\Repositories\PositionRepository
     */
    public function positions()
    {
        return $this->loadRepository(PositionRepository::class);
    }

    /**
     * Get (or create) the schema repository.
     *
     * @return \Blixt\Storage\Repositories\SchemaRepository
     */
    public function schemas()
    {
        return $this->loadRepository(SchemaRepository::class);
    }

    /**
     * Get (or create) the term repository.
     *
     * @return \Blixt\Storage\Repositories\TermRepository
     */
    public function terms()
    {
        return $this->loadRepository(TermRepository::class);
    }

    /**
     * Get (or create) the word repository.
     *
     * @return \Blixt\Storage\Repositories\WordRepository
     */
    public function words()
    {
        return $this->loadRepository(WordRepository::class);
    }

    /**
     * Create and cache a repository with the given class name.
     *
     * @param string $class
     *
     * @return mixed
     */
    protected function loadRepository($class)
    {
        if (! isset($this->repositories[$class])) {
            $this->repositories[$class] = new $class($this);
        }

        return $this->repositories[$class];
    }
}