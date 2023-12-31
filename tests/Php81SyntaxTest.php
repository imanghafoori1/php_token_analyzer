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
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $this->assertCount(3, $classRefs);
        $this->assertEquals('Hello1', $classRefs[0][0][1]);
        $this->assertEquals('Hello2', $classRefs[1][0][1]);
        $this->assertEquals('Hello3', $classRefs[2][0][1]);
        $this->assertCount(0, $attributeRefs);
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
        [$classRefs, $namespace, $attributeRefs,] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('MyTrait', $classRefs[0][0][1]);
        $this->assertEquals('Foo\Test', $classRefs[1][0][1]);
        $this->assertEquals('A', $classRefs[2][0][1]);
        $this->assertEquals('C', $classRefs[3][0][1]);
        $this->assertEquals('B', $classRefs[4][0][1]);
        $this->assertEquals('B', $classRefs[5][0][1]);
        $this->assertEquals('A', $classRefs[6][0][1]);
        $this->assertEquals('A', $classRefs[7][0][1]);
        $this->assertEquals('A', $classRefs[8][0][1]);
        $this->assertEquals('B', $classRefs[9][0][1]);
        $this->assertEquals('C', $classRefs[10][0][1]);
        $this->assertCount(11, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }

    /** @test */
    public function intersection_types_in_typehinted_properties()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php81/intersection_type.stub');
        [$classRefs, $namespace, $attributeRefs,] = ClassReferenceFinder::process($tokens);
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
        $this->assertEquals($expected, $classRefs);
        $this->assertCount(0, $attributeRefs);
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
