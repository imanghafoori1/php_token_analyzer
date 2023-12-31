<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\GetClassProperties;
use Imanghafoori\TokenAnalyzer\ParseUseStatement;

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
        $tokens = $this->getTokens(__DIR__.'/stubs/php81/readonly_property.stub');
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
    public function traits_enum()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/used_trait_enum.stub');
        [$output, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('MyTrait', $output[0][0][1]);
        $this->assertEquals('Foo\Test', $output[1][0][1]);
        $this->assertEquals('A', $output[2][0][1]);
        $this->assertEquals('C', $output[3][0][1]);
        $this->assertEquals('B', $output[4][0][1]);
        $this->assertEquals('B', $output[5][0][1]);
        $this->assertEquals('A', $output[6][0][1]);
        $this->assertEquals('A', $output[7][0][1]);
        $this->assertEquals('A', $output[8][0][1]);
        $this->assertEquals('B', $output[9][0][1]);
        $this->assertEquals('C', $output[10][0][1]);
        $this->assertCount(11, $output);
    }

    /** @test */
    public function intersection_types_in_typehinted_properties()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php81/intersection_type.stub');
        [$actualResult, $namespace] = ClassReferenceFinder::process($tokens);
        $expected = [
            [
                [0 => T_STRING, 1 => "H1", 2 => 5],
            ],
            [
                [0 => T_STRING, 1 => "H2", 2 => 5,],
            ],
            [
                [0 => T_STRING, 1 => "\\H3\\H4", 2 => 6,],
            ],
            [
                [0 => T_STRING, 1 => "\\tH5", 2 => 6,],
            ],
            [
                [0 => T_STRING, 1 => "tH6", 2 => 7,],
            ],
            [
                [0 => T_STRING, 1 => "\\tH7", 2 => 7,],
            ],
        ];
        $this->assertEquals($expected, $actualResult);
    }

    /** @test */
    public function can_find_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php81/class_references.stub');
        [$classes, $namespace] = ParseUseStatement::findClassReferences($tokens);
        $h = 0;

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Y',
            'line' => 7,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\R',
            'line' => 7,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\A\ReturnType',
            'line' => 7,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\F',
            'line' => 9,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\C',
            'line' => 9,
        ], $classes[$h]);
    }
}
