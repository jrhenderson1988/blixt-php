<?php

namespace Blixt\Search\Query;

use Blixt\Search\Query\Clause\Clause;
use Blixt\Search\Query\Clause\MustClause;
use Blixt\Search\Query\Clause\MustNotClause;
use Blixt\Search\Query\Clause\ShouldClause;
use Blixt\Stemming\Stemmer;
use Blixt\Tokenization\Token;
use Blixt\Tokenization\Tokenizer;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class QueryParser
{
    /**
     * @var \Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    /**
     * Array which maps each prefix to the relevant clause type.
     *
     * @var array
     */
    protected $prefixMap = [
        '+' => 'Must',
        '-' => 'MustNot'
    ];

    /**
     * QueryParser constructor.
     *
     * @param \Blixt\Tokenization\Tokenizer $tokenizer
     * @param \Blixt\Stemming\Stemmer $stemmer
     */
    public function __construct(Tokenizer $tokenizer, Stemmer $stemmer)
    {
        $this->tokenizer = $tokenizer;
        $this->stemmer = $stemmer;
    }

    /**
     * Parse the input search phrase into a relevant query object.
     *
     * @param string $terms
     *
     * @return \Blixt\Search\Query\Query
     */
    public function parse(string $terms): Query
    {
        $prefixes = Collection::make([]);

        $this->tokenizer->tokenize($terms, $this->getPrefixes())
            ->map(function (Token $token) use (&$prefixes) {
                $stem = $this->stemmer->stem($token->getText());

                $values = $prefixes->get($stem, []);
                if (! empty($prefix = $token->getPrefix()) && ! in_array($prefix, $values)) {
                    $values[] = $prefix;
                }

                $prefixes->put($stem, $values);
            });

        return new BooleanQuery($prefixes->map(function ($prefixes, $text) {
            return $this->createClause($text, $prefixes);
        }));
    }

    /**
     * Get the set of supported prefixes.
     *
     * @return array
     */
    public function getPrefixes()
    {
        return array_keys($this->prefixMap);
    }

    /**
     * Create the relevant clause for the given token.
     *
     * @param string $text
     * @param array $prefixes
     *
     * @return \Blixt\Search\Query\Clause\Clause
     */
    protected function createClause(string $text, array $prefixes): Clause
    {
        $prefix = count($prefixes) === 1 ? $prefixes[0] : null;
        $type = ! empty($prefix) && isset($this->prefixMap[$prefix]) ? $this->prefixMap[$prefix] : 'Should';
        $method = 'create' . $type . 'Clause';

        if (method_exists($this, $method)) {
            return $this->$method($text);
        }

        throw new InvalidArgumentException('Method [' . $method . '] does not exist.');
    }

    /**
     * Create a MustClause for the given token.
     *
     * @param string $text
     *
     * @return \Blixt\Search\Query\Clause\MustClause
     */
    protected function createMustClause(string $text): MustClause
    {
        return new MustClause($text);
    }

    /**
     * Create a MustNotClause for the given token.
     *
     * @param string $text
     *
     * @return \Blixt\Search\Query\Clause\MustNotClause
     */
    protected function createMustNotClause(string $text): MustNotClause
    {
        return new MustNotClause($text);
    }

    /**
     * Create a ShouldClause for the given token.
     *
     * @param string $text
     *
     * @return \Blixt\Search\Query\Clause\ShouldClause
     */
    protected function createShouldClause(string $text): ShouldClause
    {
        return new ShouldClause($text);
    }
}
