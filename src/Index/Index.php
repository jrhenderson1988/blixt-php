<?php

namespace Blixt\Index;

use Blixt\Document\Indexable;
use Blixt\Exceptions\DocumentAlreadyExistsException;
use Blixt\Exceptions\InvalidDocumentException;
use Blixt\Exceptions\InvalidSchemaException;
use Blixt\Search\IndexSearcher;
use Blixt\Search\Query\Query;
use Blixt\Persistence\Entities\Column;
use Blixt\Persistence\Entities\Document;
use Blixt\Persistence\Entities\Field;
use Blixt\Persistence\Entities\Occurrence;
use Blixt\Persistence\Entities\Position;
use Blixt\Persistence\Entities\Schema;
use Blixt\Persistence\Entities\Term;
use Blixt\Persistence\Entities\Word;
use Blixt\Persistence\StorageManager;
use Blixt\Search\Query\QueryParser;
use Blixt\Search\Results\ResultSet;
use Blixt\Stemming\Stemmer;
use Blixt\Tokenization\Token;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;

class Index
{
    /**
     * @var \Blixt\Persistence\Entities\Schema
     */
    protected $schema;

    /**
     * @var \Blixt\Persistence\StorageManager
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
     * @var \Blixt\Search\IndexSearcher|null
     */
    protected $searcher;

    /**
     * @var \Blixt\Search\Query\QueryParser|null
     */
    protected $parser;

    /**
     * Index constructor.
     *
     * @param \Blixt\Persistence\Entities\Schema $schema
     * @param \Blixt\Persistence\StorageManager $storage
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     * @param \Blixt\Stemming\Stemmer $stemmer
     *
     * @throws \Blixt\Exceptions\InvalidSchemaException
     */
    public function __construct(Schema $schema, StorageManager $storage, Tokenizer $tokenizer, Stemmer $stemmer)
    {
        $this->schema = $schema;
        $this->storage = $storage;
        $this->tokenizer = $tokenizer;
        $this->stemmer = $stemmer;

        $this->loadColumns();
    }

    /**
     * @param \Blixt\Document\Indexable $indexable
     *
     * @return bool
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     * @throws \Blixt\Exceptions\InvalidDocumentException
     * @throws \Blixt\Exceptions\StorageException
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
     *
     * @return \Blixt\Search\Results\ResultSet
     */
    public function search(string $search): ResultSet
    {
        if (! $this->parser) {
            $this->parser = new QueryParser($this->tokenizer, $this->stemmer);
        }

        return $this->query($this->parser->parse($search));
    }

    /**
     * @param \Blixt\Search\Query\Query $query
     *
     * @return \Blixt\Search\Results\ResultSet
     */
    public function query(Query $query): ResultSet
    {
        if (! $this->searcher) {
            $this->searcher = new IndexSearcher($this->schema, $this->storage, $this->tokenizer);
        }

        return $this->searcher->query($query);
    }

    /**
     * Load the schema's columns if they've not yet been loaded.
     *
     * @throws \Blixt\Exceptions\InvalidSchemaException
     */
    protected function loadColumns(): void
    {
        if (! $this->schema->hasColumns()) {
            $columns = $this->storage->columns()->getBySchema($this->schema);

            if ($columns->isEmpty()) {
                throw InvalidSchemaException::noColumns();
            }

            $this->schema->setColumns($columns);
        }
    }

    /**
     * Assert that the document provided does not already exist in the index.
     *
     * @param \Blixt\Document\Indexable $indexable
     *
     * @throws \Blixt\Exceptions\DocumentAlreadyExistsException
     */
    protected function assertDocumentDoesNotExist(Indexable $indexable): void
    {
        if ($document = $this->storage->documents()->findBySchemaAndKey($this->schema, $indexable->getKey())) {
            throw new DocumentAlreadyExistsException(
                "Document '{$document->getKey()}' already exists in schema '{$this->schema->getName()}'."
            );
        }
    }

    /**
     * Ensure that the given indexable document matches the schema loaded for this Index object, i.e. all of the columns
     * defined by the schema are present as fields in the indexable document. If any of the required columns are missing
     * from the indexable document, an InvalidDocumentException is thrown.
     *
     * @param \Blixt\Document\Indexable $document
     *
     * @throws \Blixt\Exceptions\InvalidDocumentException
     */
    protected function assertDocumentMatchesSchema(Indexable $document): void
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
     * @return \Blixt\Persistence\Entities\Document
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function createDocument(Indexable $indexable): Document
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
     * @param \Blixt\Persistence\Entities\Document $document
     * @param \Blixt\Persistence\Entities\Column $column
     * @param string|mixed $content
     *
     * @return \Blixt\Persistence\Entities\Field
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function createField(Document $document, Column $column, $content): Field
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
     * @param \Blixt\Persistence\Entities\Field $field
     * @param string|mixed|null             $content
     */
    protected function indexField(Field $field, $content): void
    {
        $positions = new Collection();

        $this->tokenizer->tokenize($content)->each(function (Token $token) use (&$positions) {
            $text = $this->stemmer->stem($token->getText());

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
     * @return \Blixt\Persistence\Entities\Word
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function findOrCreateWord(string $stem): Word
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
     * @param \Blixt\Persistence\Entities\Word $word
     * @param int $fieldCount
     *
     * @return \Blixt\Persistence\Entities\Term
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function findOrCreateTerm(Word $word, int $fieldCount = 1): Term
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
     * Create an occurrence record for the given Field, Term and frequency.
     *
     * @param \Blixt\Persistence\Entities\Field $field
     * @param \Blixt\Persistence\Entities\Term $term
     * @param int $frequency
     *
     * @return \Blixt\Persistence\Entities\Occurrence
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function createOccurrence(Field $field, Term $term, int $frequency): Occurrence
    {
        return $this->storage->occurrences()->save(
            Occurrence::create($field->getId(), $term->getId(), $frequency)
        );
    }

    /**
     * Create a Position record for the given Occurrence and position.
     *
     * @param \Blixt\Persistence\Entities\Occurrence $occurrence
     * @param int $position
     *
     * @return \Blixt\Persistence\Entities\Position
     * @throws \Blixt\Exceptions\StorageException
     */
    protected function createPosition(Occurrence $occurrence, int $position): Position
    {
        return $this->storage->positions()->save(
            Position::create($occurrence->getId(), $position)
        );
    }
}
