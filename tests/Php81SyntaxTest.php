<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\GetClassProperties;

class Php81SyntaxTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (! version_compare(phpversion(), '8.1.0', '>=')) {
            $this->markTestSkipped('Your php version is less than 8.1');
        }
    }

    /** @test */
    public function readonly_properties()
    {
        $string = file_get_contents(__DIR__.'/stubs/php81/readonly_property.stub');
        $tokens = token_get_all($string);
        [$output, $namespace] = ClassReferenceFinder::process($tokens);
        $this->assertCount(3, $output);
        $this->assertEquals('Hello1', $output[0][0][1]);
        $this->assertEquals('Hello2', $output[1][0][1]);
        $this->assertEquals('Hello3', $output[2][0][1]);
    }

    /** @test */
    public function enums()
    {
        [$namespace, $name, $type, $parent, $interfaces] = GetClassProperties::fromFilePath(__DIR__.'/stubs/php81/sample_enum.stub');

        $this->assertEquals("Hello", $namespace);
        $this->assertEquals('Hi', $name);
        $this->assertEquals(T_ENUM, $type);
        $this->assertEquals('', $parent);
        $this->assertEquals('', $interfaces);
    }

    /** @test */
    public function intersection_types_in_typehinted_properties()
    {
        $string = file_get_contents(__DIR__.'/stubs/php81/intersection_type.stub');
        $tokens = token_get_all($string);
        [$actualResult, $namespace] = ClassReferenceFinder::process($tokens);
        $expected = [
            [
                [0 => T_STRING, 1 => "H1", 2 => 5],
            ],
            [
                [0 => T_STRING, 1 => "H2", 2 => 5,],
            ],
            [
                [0 => T_NAME_FULLY_QUALIFIED, 1 => "\\H3\\H4", 2 => 6,],
            ],
            [
                [0 => T_NAME_FULLY_QUALIFIED, 1 => "\\tH5", 2 => 6,],
            ],
            [
                [0 => T_STRING, 1 => "tH6", 2 => 7,],
            ],
            [
                [0 => T_NAME_FULLY_QUALIFIED, 1 => "\\tH7", 2 => 7,],
            ],
        ];
        $this->assertEquals($expected, $actualResult);
    }
}
