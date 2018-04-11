<?php

namespace BlixtTests\Tokenization;

use Blixt\Stemming\Stemmer;
use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Token;
use BlixtTests\TestCase;
use Faker\Factory;
use Illuminate\Support\Collection;

class DefaultTokenizerTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    public function setUp()
    {
        $this->faker = Factory::create();
    }

    public function getTokenizer()
    {
        return new DefaultTokenizer(new DummyStemmer());
    }

    /** @test  */
    public function testTokenizerReturnsCollectionOfTokens()
    {
        $tokenizer = $this->getTokenizer();
        $tokenized = $tokenizer->tokenize($this->faker->sentence(10));

        $this->assertInstanceOf(Collection::class, $tokenized);

        $tokenized->each(function ($item) {
            $this->assertInstanceOf(Token::class, $item);
        });
    }

    /**
     * @test
     * @dataProvider basicSentencesProvider
     */
    public function testTokenizationOfBasicSentences($sentence, $tokens)
    {
        $tokenizer = $this->getTokenizer();
        $this->assertEquals($tokens, $tokenizer->tokenize($sentence));
    }

    public function basicSentencesProvider()
    {
        return [
            ['This is a basic test', new Collection([new Token('this', 0), new Token('is', 1), new Token('a', 2), new Token('basic', 3), new Token('test', 4)])],
            ['RandOmly capitaLiseD lettErs', new Collection([new Token('randomly', 0), new Token('capitalised', 1), new Token('letters', 2)])],
            ['w0rds with numb3rs', new Collection([new Token('w0rds', 0), new Token('with', 1), new Token('numb3rs', 2)])]
        ];
    }

    /**
     * @test
     * @dataProvider sentencesWithPunctuationProvider
     */
    public function testTokenizationOfSentencesWithPunctuation($sentence, $tokens)
    {
        $tokenizer = $this->getTokenizer();
        $this->assertEquals($tokens, $tokenizer->tokenize($sentence));
    }

    public function sentencesWithPunctuationProvider()
    {
        return [
            ['Don\'t blame me!', new Collection([new Token('dont', 0), new Token('blame', 1), new Token('me', 2)])],
            ['Well, this is fun.', new Collection([new Token('well', 0), new Token('this', 1), new Token('is', 2), new Token('fun', 3)])],
        ];
    }
}

class DummyStemmer implements Stemmer
{
    public function stem(string $word): string
    {
        return $word;
    }
}