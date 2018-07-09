<?php

namespace BlixtTests\Search\Query;

use Blixt\Search\Query\BooleanQuery;
use Blixt\Search\Query\Clause\Clause;
use Blixt\Search\Query\Clause\MustClause;
use Blixt\Search\Query\Clause\MustNotClause;
use Blixt\Search\Query\Clause\ShouldClause;
use Blixt\Search\Query\QueryParser;
use Blixt\Stemming\Stemmer;
use Blixt\Tokenization\Token;
use Blixt\Tokenization\Tokenizer;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;
use Mockery as m;

class QueryParserTest extends TestCase
{
    /**
     * @var \Blixt\Search\Query\QueryParser
     */
    protected $parser;

    /**
     * @var \Mockery\MockInterface|\Blixt\Tokenization\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \Mockery\MockInterface|\Blixt\Stemming\Stemmer
     */
    protected $stemmer;

    public function setUp()
    {
        $this->tokenizer = m::mock(Tokenizer::class);
        $this->stemmer = m::mock(Stemmer::class);
        $this->parser = new QueryParser($this->tokenizer, $this->stemmer);
    }

    /**
     * @test
     * @covers \Blixt\Search\Query\QueryParser::parse()
     */
    public function testParseCreatesCollectionOfClauses()
    {
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['this is a test', $this->parser->getPrefixes()])
            ->andReturn(Collection::make([
                new Token('this', 0),
                new Token('is', 1),
                new Token('a', 2),
                new Token('test', 3)
            ]));

        $this->stemmer->shouldReceive('stem')->once()->with('this')->andReturn('this');
        $this->stemmer->shouldReceive('stem')->once()->with('is')->andReturn('is');
        $this->stemmer->shouldReceive('stem')->once()->with('a')->andReturn('a');
        $this->stemmer->shouldReceive('stem')->once()->with('test')->andReturn('test');

        $result = $this->parser->parse('this is a test');

        $this->assertInstanceOf(BooleanQuery::class, $result);
        $this->assertEquals(4, $result->getClauses()->count());
        $result->getClauses()->each(function ($clause) {
            $this->assertInstanceOf(Clause::class, $clause);
        });
    }

    /**
     * @test
     * @covers \Blixt\Search\Query\QueryParser::parse()
     */
    public function testParseCreatesDifferentClausesForDifferentPrefixes()
    {
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['+one two -three', $this->parser->getPrefixes()])
            ->andReturn(Collection::make([
                new Token('one', 0, '+'),
                new Token('two', 1),
                new Token('three', 2, '-')
            ]));

        $this->stemmer->shouldReceive('stem')->once()->withArgs(['one'])->andReturn('one');
        $this->stemmer->shouldReceive('stem')->once()->withArgs(['two'])->andReturn('two');
        $this->stemmer->shouldReceive('stem')->once()->withArgs(['three'])->andReturn('three');

        $result = $this->parser->parse('+one two -three');

        $this->assertInstanceOf(BooleanQuery::class, $result);
        $this->assertEquals(3, $result->getClauses()->count());
        $this->assertInstanceOf(MustClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'one';
        }));
        $this->assertInstanceOf(ShouldClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'two';
        }));
        $this->assertInstanceOf(MustNotClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'three';
        }));
    }

    /**
     * @test
     * @covers \Blixt\Search\Query\QueryParser::parse()
     */
    public function testParseCreatesClausesWithNoDuplicateTerms()
    {
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['Test is a test', $this->parser->getPrefixes()])
            ->andReturn(Collection::make([
                new Token('test', 0),
                new Token('is', 1),
                new Token('a', 2),
                new Token('test', 3)
            ]));

        $this->stemmer->shouldReceive('stem')->twice()->withArgs(['test'])->andReturn('test');
        $this->stemmer->shouldReceive('stem')->once()->withArgs(['is'])->andReturn('is');
        $this->stemmer->shouldReceive('stem')->once()->withArgs(['a'])->andReturn('a');

        $result = $this->parser->parse('Test is a test');

        $this->assertInstanceOf(BooleanQuery::class, $result);
        $this->assertEquals(3, $result->getClauses()->count());
        $this->assertInstanceOf(ShouldClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'test';
        }));
        $this->assertInstanceOf(ShouldClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'is';
        }));
        $this->assertInstanceOf(ShouldClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'a';
        }));
    }

    /**
     * @test
     * @covers \Blixt\Search\Query\QueryParser::parse()
     */
    public function testParsePrioritisesPrefixedTermsOverDuplicateNonPrefixedTerms()
    {
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['+one one two -two', $this->parser->getPrefixes()])
            ->andReturn(Collection::make([
                new Token('one', 0, '+'),
                new Token('one', 1),
                new Token('two', 2),
                new Token('two', 3, '-'),
            ]));

        $this->stemmer->shouldReceive('stem')->twice()->withArgs(['one'])->andReturn('one');
        $this->stemmer->shouldReceive('stem')->twice()->withArgs(['two'])->andReturn('two');

        $result = $this->parser->parse('+one one two -two');

        $this->assertInstanceOf(BooleanQuery::class, $result);
        $this->assertEquals(2, $result->getClauses()->count());
        $this->assertInstanceOf(MustClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'one';
        }));
        $this->assertInstanceOf(MustNotClause::class, $result->getClauses()->first(function (Clause $clause) {
            return $clause->getValue() === 'two';
        }));
        $this->assertNull($result->getClauses()->first(function (Clause $clause) {
            return $clause instanceof ShouldClause;
        }));
    }

    /**
     * @test
     * @covers \Blixt\Search\Query\QueryParser::parse()
     */
    public function testParseSimplifiesConflictingMustAndMustNotClausesToShouldClauses()
    {
        $this->tokenizer->shouldReceive('tokenize')
            ->once()
            ->withArgs(['+test -test +test', $this->parser->getPrefixes()])
            ->andReturn(Collection::make([
                new Token('test', 0, '+'),
                new Token('test', 1, '-'),
                new Token('test', 2, '+')
            ]));

        $this->stemmer->shouldReceive('stem')->times(3)->withArgs(['test'])->andReturn('test');

        $result = $this->parser->parse('+test -test +test');

        $this->assertInstanceOf(BooleanQuery::class, $result);
        $this->assertEquals(1, $result->getClauses()->count());
        $this->assertInstanceOf(ShouldClause::class, $result->getClauses()->first());
    }
}