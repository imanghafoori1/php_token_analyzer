<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassMethods;
use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ParseUseStatement;

class Php80SyntaxTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (! version_compare(phpversion(), '8.0', '>=')) {
            $this->markTestSkipped('Your php version is less than 8.0');
        }
    }

    public function test_annotations()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php80/php8_attributes.stub');
        [$classRefs, $namespace, $attributeRefs,] = ClassReferenceFinder::process($tokens);

        $expected = [
            [[T_STRING, "FooAttribute", 3]],
            [[T_STRING, "BarClassAttrib", 4]],
            [[T_STRING, "ConstAttr", 6]],
            [[T_STRING, "FooAttribute", 8]],
            [[T_STRING, "PropAttr", 11]],
            [[T_STRING, "Foo", 11]],
            [[T_NAME_QUALIFIED, "SomeoneElse\FooMethodAttrib", 14]],
            [[T_STRING, "FooClassAttrib", 15]],
            [[T_STRING, "FooAttribute", 18]],
            [[T_STRING, "FooParamAttrib", 19]],
        ];

        $this->assertEquals($expected, $attributeRefs);
        $this->assertCount(0, $classRefs);
    }

    public function test_can_detect_class_general_test()
    {
        $class = ClassMethods::read($this->getTokens(__DIR__.'/stubs/php80/union_types.stub'));

        $this->assertEquals([T_STRING, 'sample_class', 5], $class['name']);
        $this->assertEquals(T_CLASS, $class['type']);
        $this->assertFalse($class['is_abstract']);
        $this->assertFalse($class['is_final']);
        $this->assertCount(1, $class['methods']);
    }

    public function test_can_detect_class_methods_test()
    {
        $class = ClassMethods::read($this->getTokens(__DIR__.'/stubs/php80/union_types.stub'));
        $methods = $class['methods'];

        $this->assertEquals([T_STRING, '__construct', 7], $methods[0]['name']);
        $this->assertEquals([T_PUBLIC, 'public', 7], $methods[0]['visibility']);
        $this->assertEquals('', $methods[0]['body']);
        $this->assertFalse($methods[0]['is_final']);
        $this->assertFalse($methods[0]['is_abstract']);
        $this->assertFalse($methods[0]['is_static']);
        $this->assertFalse($methods[0]['nullable_return_type']);
    }

    public function test_can_detect_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens(__DIR__.'/stubs/php80/union_types.stub'));
        $methods = $class['methods'];

        $this->assertEquals('G1', $methods[0]['returnType'][0][1]);
        $this->assertEquals('G2', $methods[0]['returnType'][1][1]);
        $this->assertEquals('G3', $methods[0]['returnType'][2][1]);
    }

    public function test_can_detect_methods_signature_test()
    {
        $class = ClassMethods::read($this->getTokens(__DIR__.'/stubs/php80/union_types.stub'));
        $methods = $class['methods'];

        $this->assertEquals('private', $methods[0]['signature'][0][1]);
        $this->assertEquals(' ', $methods[0]['signature'][1][1]);
        $this->assertEquals('Hello', $methods[0]['signature'][2][1]);
        $this->assertEquals(' ', $methods[0]['signature'][3][1]);
        $this->assertEquals('$foo', $methods[0]['signature'][4][1]);
        $this->assertEquals(',', $methods[0]['signature'][5]);
        $this->assertEquals(' ', $methods[0]['signature'][6][1]);
        $this->assertEquals('public', $methods[0]['signature'][7][1]);
        $this->assertEquals(' ', $methods[0]['signature'][8][1]);
        $this->assertEquals('int', $methods[0]['signature'][9][1]);
        $this->assertEquals('|', $methods[0]['signature'][10]);
        $this->assertEquals('float', $methods[0]['signature'][11][1]);
        $this->assertEquals(' ', $methods[0]['signature'][12][1]);
        $this->assertEquals('$x', $methods[0]['signature'][13][1]);
        $this->assertEquals(' ', $methods[0]['signature'][14][1]);
        $this->assertEquals('=', $methods[0]['signature'][15]);
        $this->assertEquals(' ', $methods[0]['signature'][16][1]);
        $this->assertEquals('0.0', $methods[0]['signature'][17][1]);
    }

    public function test_can_detect_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php80/class_references.stub');
        [$classRefs, $namespace, $attributeRefs,] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, '\A\ParentClass', 7],], $classRefs[0]);
        $this->assertEquals([[T_STRING, '\Inline\InterF3', 7]], $classRefs[1]);
        $this->assertEquals([[T_STRING, 'Finder', 12]], $classRefs[2]);
        $this->assertEquals([[T_STRING, '\Exception', 13]], $classRefs[3]);
        $this->assertEquals([[T_STRING, '\ErrorException', 13]], $classRefs[4]);
        $this->assertEquals([[T_STRING, '\YetAnotherclass', 17]], $classRefs[5]);
        $this->assertEquals([[T_STRING, 'HalfImported\TheRest', 19]], $classRefs[6]);
        $this->assertEquals([[T_STRING, 'A\Newed', 24],], $classRefs[7]);
        $this->assertEquals([[T_STRING, '\A\ReturnType', 27]], $classRefs[8]);
        $this->assertEquals([[T_STRING, 'F', 29]], $classRefs[9]);
        $this->assertEquals([[T_STRING, 'a\a', 30]], $classRefs[10]);
        $this->assertEquals([[T_STRING, 'b\b', 30]], $classRefs[11]);
        $this->assertEquals([[T_STRING, 'ParentOfAnonymous', 31]], $classRefs[12]);
        $this->assertEquals([[T_STRING, 'interfaceOfAnonymous', 31]], $classRefs[13]);
        $this->assertEquals([[T_STRING, '\T', 32]], $classRefs[14]);
        $this->assertEquals([[T_STRING, '\interfaceOfAnonymous', 34]], $classRefs[15]);
        $this->assertCount(0, $attributeRefs);
    }

    public function test_can_find_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php80/class_references.stub');
        [$classes, $namespace] = ParseUseStatement::findClassReferences($tokens);
        $h = 0;

        $this->assertEquals([
            'class' => "\A\ParentClass",
            'line' => 7,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "\Inline\InterF3",
            'line' => 7,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "Symfony\Component\Finder\Symfony\Component\Finder\Finder",
            'line' => 12,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "\Exception",
            'line' => 13,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "\ErrorException",
            'line' => 13,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "\YetAnotherclass",
            'line' => 17,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\HalfImported\TheRest',
            'line' => 19,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\A\Newed',
            'line' => 24,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\A\ReturnType',
            'line' => 27,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\F',
            'line' => 29,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\a\a',
            'line' => 30,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\b\b',
            'line' => 30,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\ParentOfAnonymous',
            'line' => 31,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\interfaceOfAnonymous',
            'line' => 31,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\T',
            'line' => 32,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\interfaceOfAnonymous',
            'line' => 34,
        ], $classes[$h]);
    }
}
