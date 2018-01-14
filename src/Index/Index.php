<?php

namespace Blixt\Index;

use Blixt\Index\Document\Document as IndexableDocument;
use Blixt\Index\Document\Field as IndexableField;
use Blixt\Index\Schema\Column as SchemaColumn;
use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Field;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\UndefinedSchemaException;
use Blixt\Index\Schema\Schema;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Token;
use Blixt\Tokenization\Tokenizer;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Index
{
    /**
     * @var \Blixt\Storage\Storage
     */
    protected $entityManager;

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
     * Index constructor.
     *
     * @param \Doctrine\ORM\EntityManager   $entityManager
     * @param \Blixt\Stemming\Stemmer       $stemmer
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     */
    public function __construct(EntityManager $entityManager, Stemmer $stemmer, Tokenizer $tokenizer)
    {
        $this->entityManager = $entityManager;
        $this->stemmer = $stemmer;
        $this->tokenizer = $tokenizer;
        $this->schemas = new Collection();

        $this->createStorageIfRequired();

        $this->loadSchemas();
    }

    protected function createStorageIfRequired()
    {
        // Create the schema
    }

    protected function loadSchemas()
    {
        // Load all of the schemas from the storage, with their columns
    }

    public function createSchema(Schema $schema)
    {
        // Create a new schema record in the storage and add it to the schemas property
    }

    public function schemaExists($name)
    {
        // Tell if the given schema exists
    }

    public function add(IndexableDocument $document, $schema)
    {
        // Add the given document to the index against the given schema
    }

    public function search($query)
    {
        // SELECT d.* FROM documents AS d
        // INNER JOIN fields AS f ON f.document_id = f.id
        // INNER JOIN occurrences AS o ON o.field_id = f.id
        // WHERE o.term_id IN () AND d.schema_id IN ()
        // GROUP BY d.id

    }













//    /**
//     * @var \Blixt\Storage\Storage
//     */
//    protected $storage;
//
//    /**
//     * @var \Blixt\Stemming\Stemmer
//     */
//    protected $stemmer;
//
//    /**
//     * @var \Blixt\Tokenization\Tokenizer
//     */
//    protected $tokenizer;
//
//    /**
//     * @var \Illuminate\Support\Collection
//     */
//    protected $columns;
//
//    /**
//     * Index constructor.
//     *
//     * @param \Blixt\Stemming\Stemmer         $stemmer
//     * @param \Blixt\Tokenization\Tokenizer   $tokenizer
//     * @param \Blixt\Storage\Storage          $storage
//     * @param \Blixt\Index\Schema\Schema|null $schema
//     *
//     * @throws \Blixt\Exceptions\UndefinedSchemaException
//     */
//    public function __construct(Stemmer $stemmer, Tokenizer $tokenizer, Storage $storage, Schema $schema = null)
//    {
//        $this->storage = $storage;
//        $this->stemmer = $stemmer;
//        $this->tokenizer = $tokenizer;
//
//        $this->createIndexIfNotExists($schema);
//
//        $this->initialiseColumns();
//    }
//
//    /**
//     * Create the index if it does not already exist, initialising it with the provided schema.
//     *
//     * @param \Blixt\Index\Schema\Schema|null $schema
//     *
//     * @throws \Blixt\Exceptions\UndefinedSchemaException
//     */
//    protected function createIndexIfNotExists(Schema $schema = null)
//    {
//        if (!$this->storage->exists()) {
//            if (is_null($schema)) {
//                throw new UndefinedSchemaException(
//                    "No schema provided to create index '{$this->storage->getName()}'."
//                );
//            }
//
//            $this->storage->transaction(function () use ($schema) {
//                $this->storage->create($schema);
//            });
//
//            $schema->getColumns()->each(function (SchemaColumn $column) {
//                $this->storage->createColumn(
//                    $column->getName(), $column->isIndexed(), $column->isStored(), $column->getWeight()
//                );
//            });
//        }
//    }
//
//    /**
//     * Load the columns from the storage into the columns property. Also, key the columns by their names to make looking
//     * them up easier.
//     */
//    protected function initialiseColumns()
//    {
//        $this->columns = $this->storage->getAllColumns()->keyBy(function (Column $column) {
//            return $column->getName();
//        });
//    }
//
//    /**
//     * Add a document, or a collection of documents to the index.
//     *
//     * @param \Illuminate\Support\Collection|\Blixt\Documents\Document|array $indexables
//     *
//     * @return bool
//     */
//    public function add($indexables)
//    {
//        $indexables = $this->makeDocumentCollection($indexables);
//
//        $this->ensureDocumentsDoNotExist($indexables);
//
//        $indexables->each(function (IndexableDocument $indexable) {
//            $this->createDocument($indexable);
//        });
//
//        return true;
//    }
//
//    /**
//     * Convert the provided documents to a collection.
//     *
//     * @param \Illuminate\Support\Collection|\Blixt\Documents\Document|array $documents
//     *
//     * @return \Illuminate\Support\Collection
//     */
//    protected function makeDocumentCollection($documents)
//    {
//        if ($documents instanceof Collection) {
//            return $documents;
//        } elseif (is_array($documents)) {
//            return new Collection($documents);
//        } elseif ($documents instanceof IndexableDocument) {
//            return new Collection([$documents]);
//        }
//
//        throw new InvalidArgumentException("Expected a document, or a collection/array of documents.");
//    }
//
//    /**
//     * Ensure that each of the provided documents are not already present in the index.
//     *
//     * TODO - Some optimization, to avoid n+1 queries.
//     *
//     * @param \Illuminate\Support\Collection $documents
//     */
//    protected function ensureDocumentsDoNotExist(Collection $documents)
//    {
//        $documents->each(function (IndexableDocument $indexable) {
//            $document = $this->storage->getDocumentByKey($indexable->getKey());
//
//            if ($document) {
//                throw new DocumentAlreadyExistsException(
//                    "Document with key {$document->getKey()} already exists in {$this->getName()} index."
//                );
//            }
//        });
//    }
//
//    /**
//     * Create a document in the index, given an indexable document.
//     *
//     * @param \Blixt\Documents\Document $indexable
//     *
//     * @return \Blixt\Models\Document
//     */
//    protected function createDocument(IndexableDocument $indexable)
//    {
//        $document = $this->storage->createDocument($indexable->getKey());
//
//        $indexable->getFields()->each(function (IndexableField $field) use ($document) {
//            $this->createField($document, $field);
//        });
//
//        return $document;
//    }
//
//    /**
//     * Store and or Index (where the corresponding column defines) the provided indexable document field in the index
//     * against the provided document. Fields that do not match a given column in the index are silently ignored.
//     *
//     * @param \Blixt\Models\Document $document
//     * @param \Blixt\Documents\Field $indexableField
//     */
//    protected function createField(Document $document, IndexableField $indexableField)
//    {
//        if ($column = $this->columns->get($indexableField->getKey())) {
//            $field = $this->storage->createField(
//                $document, $column, $column->isStored() ? $indexableField->getValue() : null
//            );
//
//            if ($column->isIndexed()) {
//                $this->indexField($field, $indexableField->getValue());
//            }
//        }
//    }
//
//    /**
//     * Index the given Field. Since the field itself may not have a value stored (depending upon the value of stored for
//     * its column), we need to pass the value through as well.
//     *
//     * @param \Blixt\Models\Field $field
//     * @param mixed|string        $value
//     */
//    protected function indexField(Field $field, $value)
//    {
//        $entries = new Collection();
//
//        $this->tokenizer->tokenize($value)->each(function (Token $token) use ($field, &$entries) {
//            $stem = $this->stemmer->stem($token->getText());
//
//            $entry = $entries->get($stem, null);
//
//            $word = $entry ? $entry['word'] : $this->storage->getWordByWord($stem);
//            if (!$word) {
//                $word = $this->storage->createWord($stem);
//            }
//
//            $entries->put($stem, [
//                'word' => $entry ? $entry['word'] : $word,
//                'frequency' => ($entry ? $entry['frequency'] : 0) + 1,
//                'positions' => array_merge($entry ? $entry['positions'] : [], [$token->getPosition()])
//            ]);
//        });
//
//        // Create the presences and occurrences for each word in the field
//        $entries->each(function ($entry) use ($field) {
//            $word = $entry['word'];
//            $frequency = $entry['frequency'];
//            $positions = $entry['positions'];
//
//            $presence = $this->storage->createPresence($field, $word, $frequency);
//            foreach ($positions as $position) {
//                $this->storage->createOccurrence($presence, $position);
//            }
//        });
//    }
//
//    public function update($key, IndexableDocument $document)
//    {
//        if ($this->remove($key)) {
//            $this->add($document);
//        }
//    }
//
//    public function remove($key)
//    {
//        return true;
//    }
//
//    public function search()
//    {
//
//    }
//
//    public function destroy()
//    {
//        if (!$this->storage->exists()) {
//            return false;
//        }
//
//        return $this->storage->destroy();
//    }
//
//    /**
//     * Get the name of the index represented by the storage.
//     *
//     * @return string
//     */
//    protected function getName()
//    {
//        return $this->storage->getName();
//    }
}