<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use EternalNerd\ConfigDude\Parser;

class ParserTest extends TestCase
{
    public function testCanBeCreatedFromValidFile() :void
    {
        $parser = new Parser("test.file");
        $this->assertInstanceOf(Parser::class, $parser);
    }

    public function testCannotBeCreatedFromInvalidFile() :void
    {
        $this->expectException(InvalidArgumentException::class);
        $parser = new Parser("bad.file");
    }
}