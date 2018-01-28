<?php

namespace Blixt\Index;

use Blixt\Blixt;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Index\Document\Document as Indexable;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Entities\Word;
use Blixt\Tokenization\Token;
use Illuminate\Support\Collection;

class Index
{
    /**
     * @var \Blixt\Storage\Entities\Schema
     */
    protected $schema;

    /**
     * @var \Blixt\Storage\Storage
     */
    protected $storage;

    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    /**
     * Index constructor.
     *
     * @param \Blixt\Blixt                   $blixt
     * @param \Blixt\Storage\Entities\Schema $schema
     */
    public function __construct(Blixt $blixt, Schema $schema)
    {
        $this->storage = $blixt->getStorage();
        $this->tokenizer = $blixt->getTokenizer();
        $this->stemmer = $blixt->getStemmer();
        $this->schema = $schema;
    }

    /**
     * @param \Blixt\Index\Document\Document $indexable
     *
     * @return bool
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     */
    public function add(Indexable $indexable)
    {
        $this->assertDocumentDoesNotExist($indexable);
        $this->assertDocumentMatchesSchema($indexable);
        $this->createDocument($indexable);

        return true;
    }

    /**
     * Assert that the document provided does not already exist in the index.
     *
     * @param \Blixt\Index\Document\Document $indexable
     *
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     */
    protected function assertDocumentDoesNotExist(Indexable $indexable)
    {
        if ($document = $this->storage->documents()->findByKey($indexable->getKey())) {
            throw new DocumentAlreadyExistsException(
                "Document '{$document->getKey()}' already exists in schema '{$this->schema->getName()}'."
            );
        }
    }

    /**
     * @param \Blixt\Index\Document\Document $document
     *
     * @throws \Blixt\Exceptions\InvalidDocumentException
     */
    public function assertDocumentMatchesSchema(Indexable $document)
    {
        $fields = $document->getFields();

        $this->schema->getColumns()->each(function (Column $column) use ($fields) {
            if (! $fields->has($column->getName())) {
                throw new InvalidDocumentException(
                    "The field '{$column->getName()}' is missing from the provided document."
                );
            }
        });
    }

    /**
     * Add a document to the index for the given indexable document.
     *
     * @param \Blixt\Index\Document\Document $indexable
     *
     * @return \Blixt\Storage\Entities\Document
     */
    protected function createDocument(Indexable $indexable)
    {
        $document = $this->storage->documents()->create($this->schema, $indexable->getKey());

        $this->schema->getColumns()->each(function (Column $column) use ($document, $indexable) {
            $this->createField(
                $document,
                $column,
                $indexable->getField($column->getName())
            );
        });

        return $document;
    }

    /**
     * Create a field for the given document and column using the given field string.
     *
     * @param \Blixt\Storage\Entities\Document $document
     * @param \Blixt\Storage\Entities\Column   $column
     * @param string|mixed                     $content
     *
     * @return \Blixt\Storage\Entities\Field
     */
    protected function createField(Document $document, Column $column, $content)
    {
        $field = $this->storage->fields()->create(
            $document,
            $column->getId(),
            $column->isStored() ? $content : null
        );

        if ($column->isIndexed()) {
            $this->indexField($document, $column, $content);
        }

        return $field;
    }

    /**
     * Index the field, given as a string for the given document and column.
     *
     * TODO: Improve this method by grouping together lookups for words, terms etc (by whole field or all fields)
     *
     * @param \Blixt\Storage\Entities\Document $document
     * @param \Blixt\Storage\Entities\Column   $column
     * @param string|mixed|null                $field
     */
    protected function indexField(Document $document, Column $column, $field)
    {
        $positions = new Collection();

        $this->tokenizer->tokenize($field)->each(function (Token $token) use (&$positions) {
            $stem = $this->stemmer->stem($token->getText());

            $positions->put($stem, array_merge($positions->get($stem, []), [$token->getPosition()]));
        });

        var_dump($positions);


//        $this->tokenizer->tokenize($field)->each(function (Token $token) {
//            // - Tokenize and stem each word
//            $stem = $this->stemmer->stem($token->getText());
//
//            // - Look up or create word records and get a collection of words
//            $word = $this->findOrCreateWord($stem);
//
//            // - Look up or create term records for the schema/word and get a collection of terms
//            $term = $this->findOrCreateTerm($word);
//
//            // - Create occurrence records for each term against the field, making sure to store document counts
//            // - Create position records for each occurrence representing each terms position in the field
//            // - Update term field counts to reflect new fields added
//
//
//        });
    }

    /**
     * Find or create the given word in the index.
     *
     * @param string|mixed $word
     *
     * @return \Blixt\Storage\Entities\Word
     */
    protected function findOrCreateWord($word)
    {
        if ($found = $this->storage->words()->findByWord($word)) {
            return $found;
        }

        return $this->storage->words()->create($word);
    }

    /**
     * Find a term that matches the schema that this index represents and the given word, or if no such term exists,
     * create a new one.
     *
     * @param \Blixt\Storage\Entities\Word $word
     *
     * @return \Blixt\Storage\Entities\Term
     */
    protected function findOrCreateTerm(Word $word)
    {
        if ($term = $this->storage->terms()->findBySchemaAndWord($this->schema, $word)) {
            return $term;
        }

        return $this->storage->terms()->create($this->schema, $word);
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
