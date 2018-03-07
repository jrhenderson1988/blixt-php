<?php

namespace Blixt\Index;

use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Search\Query\Query;
use Blixt\Stemming\Stemmer;
use Blixt\Storage\Entities\Column;
use Blixt\Storage\Entities\Document;
use Blixt\Storage\Entities\Field;
use Blixt\Storage\Entities\Occurrence;
use Blixt\Storage\Entities\Position;
use Blixt\Storage\Entities\Schema;
use Blixt\Storage\Entities\Term;
use Blixt\Storage\Entities\Word;
use Blixt\Storage\Storage;
use Blixt\Tokenization\Token;
use Blixt\Tokenization\Tokenizer;
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
     * @var \Blixt\Index\IndexReader
     */
    protected $reader;

    /**
     * @var \Blixt\Index\IndexWriter
     */
    protected $writer;

    /**
     * Index constructor.
     *
     * @param \Blixt\Storage\Storage         $storage
     * @param \Blixt\Tokenization\Tokenizer  $tokenizer
     * @param \Blixt\Stemming\Stemmer        $stemmer
     * @param \Blixt\Storage\Entities\Schema $schema
     */
    public function __construct(Storage $storage, Tokenizer $tokenizer, Stemmer $stemmer, Schema $schema)
    {
        $this->storage = $storage;
        $this->tokenizer = $tokenizer;
        $this->stemmer = $stemmer;
        $this->schema = $schema;
    }

    /**
     * @param \Blixt\Index\Indexable $indexable
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
     * @param \Blixt\Search\Query\Query $query
     */
    public function search(Query $query)
    {
        // TODO
    }

    /**
     * Get (and create if necessary) the index reader.
     *
     * @return \Blixt\Index\IndexReader
     */
    public function getReader()
    {
        if (! $this->reader) {
            $this->reader = new IndexReader(
                $this->storage,
                $this->tokenizer,
                $this->stemmer,
                $this->schema
            );
        }

        return $this->reader;
    }

    /**
     * Get (and create if necessary) the index writer.
     *
     * @return \Blixt\Index\IndexWriter
     */
    public function getWriter()
    {
        if (! $this->writer) {
            $this->writer = new IndexWriter(
                $this->storage,
                $this->tokenizer,
                $this->stemmer,
                $this->schema
            );
        }

        return $this->writer;
    }

    /**
     * Assert that the document provided does not already exist in the index.
     *
     * @param \Blixt\Index\Indexable $indexable
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
     * @param \Blixt\Index\Indexable $document
     *
     * @throws \Blixt\Exceptions\InvalidDocumentException
     */
    protected function assertDocumentMatchesSchema(Indexable $document)
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
     * @param \Blixt\Index\Indexable $indexable
     *
     * @return \Blixt\Storage\Entities\Document
     */
    protected function createDocument(Indexable $indexable)
    {
        $document = $this->storage->documents()->save(
            (new Document())
                ->schemaId($this->schema->getId())
                ->key($indexable->getKey())
        );

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
        $field = $this->storage->fields()->save(
            (new Field())
                ->documentId($document->getId())
                ->columnId($column->getId())
                ->value($column->isStored() ? $content : null)
        );

        if ($column->isIndexed()) {
            $this->indexField($field, $content);
        }

        return $field;
    }

    /**
     * Index the field, given as a string for the given document and column.
     *
     * TODO: Improve this method by grouping together lookups for words, terms etc (by whole field or all fields)
     *
     * @param \Blixt\Storage\Entities\Field $field
     * @param string|mixed|null             $content
     */
    protected function indexField(Field $field, $content)
    {
        $positions = new Collection();

        $this->tokenizer->tokenize($content)->each(function (Token $token) use (&$positions) {
            $stem = $this->stemmer->stem($token->getText());

            $positions->put(
                $stem, array_merge($positions->get($stem, []), [$token->getPosition()])
            );
        });

        $positions->each(function ($positions, $stem) use ($field) {
            $term = $this->findOrCreateTerm(
                $word = $this->findOrCreateWord($stem)
            );

            $occurrence = $this->createOccurrence($field, $term, count($positions));

            foreach ($positions as $position) {
                $this->createPosition($occurrence, $position);
            }
        });
    }

    /**
     * Find or create a word for the given word stem.
     *
     * @param string $stem
     *
     * @return \Blixt\Storage\Entities\Word
     */
    protected function findOrCreateWord($stem)
    {
        $word = $this->storage->words()->findByWord($stem);

        return $word ?: $this->storage->words()->save(
            (new Word())->word($stem)
        );
    }

    /**
     * Find or create a term for the given word and the schema represented by this index, adding the provided field
     * count to any existing field count or using it as the field count for the new term.
     *
     * @param \Blixt\Storage\Entities\Word $word
     * @param int                          $fieldCount
     *
     * @return \Blixt\Storage\Entities\Term
     */
    protected function findOrCreateTerm(Word $word, $fieldCount = 1)
    {
        if ($term = $this->storage->terms()->findBySchemaAndWord($this->schema, $word)) {
            $term->setFieldCount(
                $term->getFieldCount() + $fieldCount
            );

            return $this->storage->terms()->save($term);
        }

        return $this->storage->terms()->save(
            (new Term())
                ->wordId($word->getId())
                ->schemaId($this->schema->getId())
                ->fieldCount($fieldCount)
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Field $field
     * @param \Blixt\Storage\Entities\Term  $term
     * @param int                           $frequency
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    protected function createOccurrence(Field $field, Term $term, $frequency)
    {
        return $this->storage->occurrences()->save(
            (new Occurrence())
                ->fieldId($field->getId())
                ->termId($term->getId())
                ->frequency($frequency)
        );
    }

    /**
     * @param \Blixt\Storage\Entities\Occurrence $occurrence
     * @param int                                $position
     *
     * @return \Blixt\Storage\Entities\Position
     */
    protected function createPosition(Occurrence $occurrence, $position)
    {
        return $this->storage->positions()->save(
            (new Position())
                ->occurrenceId($occurrence->getId())
                ->position($position)
        );
    }

//    /**
//     * Find or create the given set of words in the index.
//     *
//     * @param \Illuminate\Support\Collection $stemmedWords
//     *
//     * @return \Illuminate\Support\Collection
//     */
//    protected function findOrCreateWords(Collection $stemmedWords)
//    {
//        $words = $this->storage->words()->getByWords($stemmedWords);
//
//        $toCreate = $stemmedWords->diff($words->map(function (Word $word) {
//            return $word->getWord();
//        }));
//
//        $toCreate->each(function ($term) use (&$words) {
//            $words->push($this->storage->words()->create($term));
//        });
//
//        return $words;
//    }
//
//    /**
//     * Find or create terms by the given set of words and the schema this index represents.
//     *
//     * @param \Illuminate\Support\Collection $words
//     *
//     * @return \Illuminate\Support\Collection
//     */
//    protected function findOrCreateTerms(Collection $words)
//    {
//        // Get a collection of terms from the repository, matching the given words and the schema represented by this
//        // index, ensure uniqueness and then key the collection by each term's word ID for quick and easy look ups.
//        $terms = $this->storage->terms()->getBySchemaAndWords($this->schema, $words)->unique(function (Term $term) {
//            return $term->getId();
//        })->keyBy(function (Term $term) {
//            return $term->getWordId();
//        });
//
//        // From the provided collection of words, determine those that we now need to create by find out which ones
//        // aren't present in the terms collection we've just retrieved.
//        $toCreate = $words->filter(function (Word $word) use ($terms) {
//            return ! $terms->has($word->getId());
//        });
//
//        // Create each missing word using the storage repository and add it to the collection of terms, keyed by the
//        // word ID.
//        $toCreate->each(function (Word $word) use (&$terms) {
//            $terms->put(
//                $word->getId(),
//                $this->storage->terms()->create($this->schema, $word, 0)
//            );
//        });
//
//        // Return the collection of terms that were either found or created.
//        return $terms->values();
//    }

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
