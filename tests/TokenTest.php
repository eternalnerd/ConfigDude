<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use EternalNerd\ConfigDude\Token;

class TokenTest extends TestCase
{
    public function testTokenCanBeCreated() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'string',
            2 => 'min(100)',
            3 => 'min(200)',
        ];
        $token = new Token($array);
        $this->assertInstanceOf(Token::class, $token);
    }

    public function testTokenCannotBeCreatedFromEmptyArray() :void
    {
        $this->expectError(TypeError::class);
        $array = [];
        $token = new Token($array);
    }

    public function testTokenCanGetDefaultValue() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'string',
            2 => 'default="bibbidy"',
            3 => 'min(200)',
        ];
        $token = new Token($array);
        $this->assertEquals('bibbidy', $token->getDefault());
    }

    public function testTokenEmptyDefaultShouldBeFalse() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'string',
            2 => 'default=',
            3 => 'min(200)',
        ];
        $token = new Token($array);
        $this->assertEquals(false, $token->getDefault());
    }

    public function testTokenEmptyTypeShouldDefaultToString() :void
    {
        $array = [
            0 => 'This is a test'
        ];
        $token = new Token($array);
        $this->assertEquals('string', $token->getType());
    }
}