<?php

namespace BlixtTests\Tokenization;

use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Token;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class DefaultTokenizerTest extends TestCase
{
    /**
     * @var \Blixt\Tokenization\DefaultTokenizer
     */
    protected $tokenizer;

    public function setUp()
    {
        $this->tokenizer = new DefaultTokenizer();
    }

    /**
     * @test
     * @covers \Blixt\Tokenization\DefaultTokenizer::tokenize()
     */
    public function testTokenizerReturnsCollectionOfTokens()
    {
        $tokenized = $this->tokenizer->tokenize('this is a test');

        $this->assertInstanceOf(Collection::class, $tokenized);

        $tokenized->each(function ($item) {
            $this->assertInstanceOf(Token::class, $item);
        });
    }

    /**
     * @test
     * @covers \Blixt\Tokenization\DefaultTokenizer::tokenize()
     */
    public function testTokenizationOfABasicSentence()
    {
        $expected = new Collection([
            new Token('this', 0),
            new Token('is', 1),
            new Token('a', 2),
            new Token('basic', 3),
            new Token('test', 4)
        ]);

        $this->assertEquals($expected, $this->tokenizer->tokenize('This is a basic test'));
    }

    /**
     * @test
     * @covers \Blixt\Tokenization\DefaultTokenizer::tokenize()
     */
    public function testTokenizationOfABasicSentenceWithRandomlyCapitalisedLetters()
    {
        $expected = new Collection([
            new Token('randomly', 0),
            new Token('capitalised', 1),
            new Token('letters', 2)
        ]);

        $this->assertEquals($expected, $this->tokenizer->tokenize('RandOmly capitaLiseD lettErs'));
    }

    /**
     * @test
     * @covers \Blixt\Tokenization\DefaultTokenizer::tokenize()
     */
    public function testTokenizationOfABasicSentenceWithRandomlyPlacedNumbers()
    {
        $expected = new Collection([
            new Token('w0rds', 0),
            new Token('with', 1),
            new Token('numb3rs', 2)
        ]);

        $this->assertEquals($expected, $this->tokenizer->tokenize('w0rds with numb3rs'));
    }

    public function testTokenizationOfSentencesWithPunctuation()
    {
        $expected = new Collection([
            new Token('dont', 0),
            new Token('blame', 1),
            new Token('me', 2)
        ]);

        $this->assertEquals($expected, $this->tokenizer->tokenize('Don\'t blame me!'));
    }

    public function testTokenizationOfWordsWithPrefixes()
    {
        $expected = Collection::make([
            new Token('required', 0, '+'),
            new Token('prohibited', 1, '-'),
            new Token('optional', 2)
        ]);

        $this->assertEquals($expected, $this->tokenizer->tokenize('+required -prohibited optional', ['+', '-']));
    }
}
