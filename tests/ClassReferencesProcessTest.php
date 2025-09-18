<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class ClassReferencesProcessTest extends BaseTestClass
{
    /** @test */
    public function traits()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/used_trait.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

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
    public function can_instanceof()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/instanceof.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('Hello', $classRefs[0][0][1]);
        $this->assertEquals('Hello2', $classRefs[1][0][1]);
        $this->assertEquals('\Hello3', $classRefs[2][0][1]);
        $this->assertCount(3, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }

    /** @test */
    public function refs_in_flat_files()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/non_in_class_refs.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('Model\User', $classRefs[0][0][1]);
        $this->assertEquals('H', $classRefs[1][0][1]);
        $this->assertEquals('T', $classRefs[2][0][1]);
        $this->assertCount(0, $attributeRefs);
        $this->assertEquals('', $namespace);
    }

    /** @test */
    public function can_detect_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/class_references.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, 'A', 9]], $classRefs[0]);
        $this->assertEquals([[T_STRING, 'InterF1', 9]], $classRefs[1]);
        $this->assertEquals([[T_STRING, 'InterF2', 9]], $classRefs[2]);
        $this->assertEquals([[T_STRING, 'B', 9]], $classRefs[3]);

        $this->assertEquals([[T_STRING, 'Trait1', 11]], $classRefs[4]);
        $this->assertEquals([[T_STRING, 'Trait2', 11]], $classRefs[5]);
        $this->assertEquals([[T_STRING, 'Trait3', 13]], $classRefs[6]);

        $this->assertEquals([[T_STRING, 'TypeHint1', 17]], $classRefs[7]);
        $this->assertEquals([[T_STRING, 'TypeHint2', 17]], $classRefs[8]);
        $this->assertEquals([[T_STRING, 'Finder', 23]], $classRefs[9]);
        $this->assertEquals([[T_STRING, 'DirectoryNotFoundException', 31]], $classRefs[10]);
        $this->assertEquals([[T_STRING, 'MyAmIClass', 35]], $classRefs[11]);
        $this->assertEquals([[T_STRING, 'TypeHint1', 43]], $classRefs[12]);
        $this->assertEquals([[T_STRING, 'ReturnyType2', 43]], $classRefs[13]);
        $this->assertEquals([[T_STRING, 'Newed', 51]], $classRefs[14]);
        $this->assertEquals([[T_STRING, 'Newed', 51]], $classRefs[15]);

        $this->assertEquals("Imanghafoori\LaravelMicroscope\FileReaders", $namespace);
        $this->assertEquals([[T_STRING, 'InConstructor', 58]], $classRefs[16]);

        $this->assertEquals([[T_STRING, 'F', 63]], $classRefs[17]);
        $this->assertCount(0, $attributeRefs);
    }

    /** @test */
    public function can_detect_anonymous_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/anonymous_class_references.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, 'Migration', 8]], $classRefs[0]);
        $this->assertEquals([[T_STRING, 'User', 14]], $classRefs[1]);
    }

    /** @test */
    public function can_detect_inline_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/inline_class_references.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, '\A', 9]], $classRefs[0]);
        $this->assertEquals([[T_STRING, '\InterF1', 9]], $classRefs[1]);
        $this->assertEquals([[T_STRING, '\InterF2', 9]], $classRefs[2]);
        $this->assertEquals([[T_STRING, '\B', 9]], $classRefs[3]);

        $this->assertEquals([[T_STRING, '\Trait1', 11]], $classRefs[4]);
        $this->assertEquals([[T_STRING, '\Trait2', 11]], $classRefs[5]);
        $this->assertEquals([[T_STRING, '\Trait3', 13]], $classRefs[6]);

        $this->assertEquals([[T_STRING, '\Type\Hint1', 17]], $classRefs[7]);
        $this->assertEquals([[T_STRING, '\TypeHint2', 17]], $classRefs[8]);
        $this->assertEquals([[T_STRING, '\Finder', 23]], $classRefs[9]);
        $this->assertEquals([[T_STRING, '\DirectoryNotFoundException', 31]], $classRefs[10]);
        $this->assertEquals([[T_STRING, '\MyAmIClass', 35]], $classRefs[11]);
        $this->assertEquals([[T_STRING, '\TypeHint1', 43]], $classRefs[12]);
        $this->assertEquals([[T_STRING, '\ReturnyType2', 43]], $classRefs[13]);
        $this->assertEquals([[T_STRING, '\Newed', 51]], $classRefs[14]);
        $this->assertEquals([[T_STRING, '\Newed', 51]], $classRefs[15]);

        $this->assertEquals("Imanghafoori\LaravelMicroscope\FileReaders", $namespace);
        $this->assertEquals([[T_STRING, '\InConstructor', 58]], $classRefs[16]);

        $this->assertEquals([[T_STRING, '\F', 63]], $classRefs[17]);
        $this->assertEquals([[T_STRING, '\iteable', 66]], $classRefs[18]);

        $this->assertEquals([[T_STRING, '\countable', 66]], $classRefs[19]);
        $this->assertEquals([[T_STRING, '\User', 68]], $classRefs[20]);
        $this->assertEquals([[T_STRING, '\ParentOfAnonymous', 77]], $classRefs[21]);
        $this->assertEquals([[T_STRING, '\interfaceOfAnonymous', 78]], $classRefs[22]);
        $this->assertEquals([[T_STRING, '\A\interfaceOfAnonymous', 79]], $classRefs[23]);
        $this->assertCount(0, $attributeRefs);
    }

    /** @test */
    public function namespaced_function_call()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/namespaced_function_call.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);
        $expected = [
            [
                [
                    0 => T_STRING,
                    1 => "Name\Spaced\classy\Call",
                    2 => 5,
                ],
            ],
            [
                [
                    0 => T_STRING,
                    1 => "Name\Spaced\classy\Call",
                    2 => 5,
                ],
            ],
        ];

        $this->assertEquals($expected, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }

    /**
     * @test
     */
    public function test_new_alias()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/new_alias.stub');
        [$classRefs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([
            [
                [T_STRING, 'Resource', 7],
            ],
        ], $classRefs);
    }
}
