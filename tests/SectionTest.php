<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use EternalNerd\ConfigDude\Section;
use EternalNerd\ConfigDude\Token;

class SectionTest extends TestCase
{
    public function testSectionCanBeCreated() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'repeatable',
            2 => 'min(100)',
            3 => 'min(200)',
        ];
        $section = new Section($array);
        $this->assertInstanceOf(Section::class, $section);
    }

    public function testSectionCannotBeCreatedFromEmptyArray() :void
    {
        $this->expectError(TypeError::class);
        $array = [];
        $section = new Section($array);
    }

    public function testSectionCanAddChild() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'repeatable',
            2 => 'min(1)',
            3 => 'min(5)',
        ];
        $child = [
            0 => 'This is a test',
            1 => 'string',
            2 => 'default=',
            3 => 'min(200)',
        ];
        $section = new Section($array);
        $this->assertEquals(true, $section->addChild($child));
    }

    public function testSectionChildIsToken() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'repeatable',
            2 => 'min(1)',
            3 => 'min(5)',
        ];
        $child = [
            0 => 'This is a test',
            1 => 'string',
            2 => 'default=',
            3 => 'min(200)',
        ];
        $section = new Section($array);
        $token = new Token($child);
        $section->addChild($token);
        foreach($section->getChildren() as $childObject){
            $this->assertInstanceOf(Token::class, $childObject);
        }
    }

    public function testSectionChildExistsReturnTrue() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'repeatable',
            2 => 'min(1)',
            3 => 'min(5)',
        ];
        $child = [
            0 => 'This is a test',
            1 => 'string',
            2 => 'default=',
            3 => 'min(200)',
        ];
        $section = new Section($array);
        $token = new Token($child);
        $section->addChild($token);
        $this->assertEquals(true, $section->childExists("thisIsATest"));
    }

    public function testSectionChildDoesntExistReturnsFalse() :void
    {
        $array = [
            0 => 'This is a test',
            1 => 'repeatable',
            2 => 'min(1)',
            3 => 'min(5)',
        ];
        $child = [
            0 => 'This is a test',
            1 => 'string',
            2 => 'default=',
            3 => 'min(200)',
        ];
        $section = new Section($array);
        $token = new Token($child);
        $section->addChild($token);
        $this->assertEquals(false, $section->childExists("daBuDaBu"));
    }
}