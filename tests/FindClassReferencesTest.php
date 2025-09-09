<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\GetClassProperties;
use Imanghafoori\TokenAnalyzer\ParseUseStatement;

class FindClassReferencesTest extends BaseTestClass
{
    public function test_can_find_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/class_references.stub');
        [$classes, $namespace] = ParseUseStatement::findClassReferences($tokens);

        $this->assertEquals("Imanghafoori\LaravelMicroscope\FileReaders", $namespace);
        $h = 0;
        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\A',
            'line' => 9,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\InterF1',
            'line' => 9,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\InterF2',
            'line' => 9,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\B',
            'line' => 9,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Trait1',
            'line' => 11,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Trait2',
            'line' => 11,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Trait3',
            'line' => 13,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "Imanghafoori\LaravelMicroscope\FileReaders\TypeHint1",
            'line' => 17,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "Imanghafoori\LaravelMicroscope\FileReaders\TypeHint2",
            'line' => 17,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "Symfony\Component\Finder\Symfony\Component\Finder\Finder",
            'line' => 23,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "Symfony\Component\Finder\Exception\DirectoryNotFoundException",
            'line' => 31,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "Imanghafoori\LaravelMicroscope\FileReaders\MyAmIClass",
            'line' => 35,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\TypeHint1',
            'line' => 43,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\ReturnyType2',
            'line' => 43,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Newed',
            'line' => 51,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Newed',
            'line' => 51,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\InConstructor',
            'line' => 58,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\F',
            'line' => 63,
        ], $classes[$h++]);

       $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\iteable',
            'line' => 66,
        ], $classes[$h++]);

       $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\countable',
            'line' => 66,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\User',
            'line' => 68,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\ParentOfAnonymous',
            'line' => 77,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\interfaceOfAnonymous',
            'line' => 78,
        ], $classes[$h]);
    }

    public function test_function_return_typehint()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/multi_return_types.stub');
        [$classRefs, $namespace, $attributeRefs,] = ClassReferenceFinder::process($tokens);

        $expected = [
            [
                [T_STRING, '\E', 5],
            ],
            [
                [T_STRING, 'F', 5],
            ],
            [
                [T_STRING, '\A\B', 5],
            ],
            [
                [T_STRING, 'Y\T', 5],
            ],
        ];

        $this->assertEquals($expected, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }

    public function test_can_detected_references_in_array()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/references_in_array.stub');
        [$classRefs, $namespace, $attributeRefs,] = ClassReferenceFinder::process($tokens);

        $expected = [
            [
                [T_STRING, 'T1', 4],
            ],
            [
                [T_STRING, 'T2', 4],
            ],
            [
                [T_STRING, 'T3', 4],
            ],
            [
                [T_STRING, 'T33', 4],
            ],
            [
                [T_STRING, 'User', 8],
            ],
            [
                [T_STRING, 'Product', 8],
            ],
            [
                [T_STRING, 'Exam', 8],
            ],
            [
                [T_STRING, 'BBB', 18],
            ],
        ];

        $this->assertEquals($expected, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }

    public function test_multi_extend()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/multi_extend_interface.stub');

        [$classes, $namespace, $refs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([
            [
                [T_STRING, "AnotherBaseInterface", 7]
            ],
            [
                [T_STRING, "Arrayable", 7]
            ],
            [
                [T_STRING, "Jsonable", 7]
            ],
            [
                [T_STRING, "JsonSerializable", 7]
            ],
        ], $classes);

        $this->assertEquals('App\Models\Support', $namespace);
    }
}
