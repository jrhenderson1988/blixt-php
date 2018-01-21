<?php

namespace BlixtTests\Tokenization;

use Blixt\Tokenization\DefaultTokenizer;
use Blixt\Tokenization\Token;
use BlixtTests\TestCase;
use Illuminate\Support\Collection;

class DefaultTokenizerTest extends TestCase
{
    /** @test  */
    public function testTokenizerReturnsCollectionOfTokens()
    {
        $tokenizer = new DefaultTokenizer();
        $tokenized = $tokenizer->tokenize($this->faker->sentence(10));

        $this->assertInstanceOf(Collection::class, $tokenized);

        $tokenized->each(function ($item) {
            $this->assertInstanceOf(Token::class, $item);
        });
    }

    /** @test */
    public function testTokenizationOfBasicSentences()
    {
        $sentences = [
            'This is a basic test' => new Collection([
                new Token('this', 0),
                new Token('is', 1),
                new Token('a', 2),
                new Token('basic', 3),
                new Token('test', 4)
            ]),
            'RandOmly capitaLiseD lettErs' => new Collection([
                new Token('randomly', 0),
                new Token('capitalised', 1),
                new Token('letters', 2)
            ]),
            'w0rds with numb3rs' => new Collection([
                new Token('w0rds', 0),
                new Token('with', 1),
                new Token('numb3rs', 2)
            ])
        ];

        $tokenizer = new DefaultTokenizer();
        foreach ($sentences as $sentence => $expected) {
            $this->assertEquals($expected, $tokenizer->tokenize($sentence));
        }
    }

    /** @test */
    public function testTokenizationOfSentencesWithPunctuation()
    {
        $sentences = [
            'Don\'t blame me!' => new Collection([
                new Token('dont', 0), new Token('blame', 1), new Token('me', 2)
            ]),
            'Well, this is fun.' => new Collection([
                new Token('well', 0), new Token('this', 1), new Token('is', 2), new Token('fun', 3)
            ]),
        ];

        $tokenizer = new DefaultTokenizer();

        foreach ($sentences as $sentence => $expected) {
            $this->assertEquals($expected, $tokenizer->tokenize($sentence));
        }
    }
}