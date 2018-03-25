<?php

namespace Blixt\Index;

use Blixt\Document\Indexable;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Exceptions\InvalidSchemaException;
use Blixt\Search\Query\Query;
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
     * Index constructor.
     *
     * @param \Blixt\Storage\Entities\Schema $schema
     * @param \Blixt\Storage\Storage         $storage
     * @param \Blixt\Tokenization\Tokenizer  $tokenizer
     *
     * @throws \Blixt\Exceptions\InvalidSchemaException
     */
    public function __construct(Schema $schema, Storage $storage, Tokenizer $tokenizer)
    {
        $this->storage = $storage;
        $this->tokenizer = $tokenizer;
        $this->schema = $schema;

        $this->loadColumns();
    }

    /**
     * Load the schema's columns if they've not yet been loaded.
     *
     * @throws \Blixt\Exceptions\InvalidSchemaException
     */
    protected function loadColumns(): void
    {
        if ($this->schema->getColumns()->isEmpty()) {
            $columns = $this->storage->columns()->getBySchema($this->schema);

            if ($columns->isEmpty()) {
                throw InvalidSchemaException::noColumns();
            }

            $this->schema->setColumns($columns);
        }
    }

    /**
     * @param \Blixt\Document\Indexable $indexable
     *
     * @return bool
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     */
    public function add(Indexable $indexable): bool
    {
        $this->assertDocumentDoesNotExist($indexable);
        $this->assertDocumentMatchesSchema($indexable);
        $this->createDocument($indexable);

        return true;
    }

    /**
     * Translate the given search string into a query object and run it.
     *
     * @param string $search
     */
    public function search(string $search)
    {

    }

    /**
     * @param \Blixt\Search\Query\Query $query
     */
    public function query(Query $query)
    {

    }

    /**
     * Assert that the document provided does not already exist in the index.
     *
     * @param \Blixt\Document\Indexable $indexable
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
     * @param \Blixt\Document\Indexable $document
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
     * @param \Blixt\Document\Indexable $indexable
     *
     * @return \Blixt\Storage\Entities\Document
     */
    protected function createDocument(Indexable $indexable)
    {
        $document = $this->storage->documents()->save(
            Document::create($this->schema->getId(), $indexable->getKey())
        );

        $this->schema->getColumns()->each(function (Column $column) use ($document, $indexable) {
            $this->createField($document, $column, $indexable->getField($column->getName()));
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
            Field::create($document->getId(), $column->getId(), $column->isStored() ? $content : null)
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
            $text = $token->getText();

            $positions->put(
                $text, array_merge($positions->get($text, []), [$token->getPosition()])
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
            Word::create($stem)
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
            Term::create($this->schema->getId(), $word->getId(), $fieldCount)
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
            Occurrence::create($field->getId(), $term->getId(), $frequency)
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
            Position::create($occurrence->getId(), $position)
        );
    }
}
