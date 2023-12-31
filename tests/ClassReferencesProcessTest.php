<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\DocblockReader;

class ClassReferencesProcessTest extends BaseTestClass
{
    /** @test */
    public function traits()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/used_trait.stub');
        [$classRefs, $attributeRefs,] = ClassReferenceFinder::process($tokens);

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
        [$classRefs, $attributeRefs,] = ClassReferenceFinder::process($tokens);

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
        [$classRefs, $attributeRefs, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals("Model\User", $classRefs[0][0][1]);
        $this->assertEquals("H", $classRefs[1][0][1]);
        $this->assertEquals("T", $classRefs[2][0][1]);
        $this->assertCount(0, $attributeRefs);
        $this->assertEquals("", $namespace);
    }

    /** @test */
    public function can_detect_docblocks()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/doc_block_ref.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);

        $this->assertEquals( ["class" => "A", "line" => 5], $output[0]);
        $this->assertEquals( ["class" => "Logger", "line" => 9], $output[1]);
        $this->assertEquals( ["class" => "Hello", "line" => 14], $output[2]);
        $this->assertEquals( ["class" => "Hello3", "line" => 14], $output[3]);
        $this->assertEquals( ["class" => "Hello2", "line" => 14], $output[4]);
        $this->assertEquals( ["class" => "ArrayIterator", "line" => 14], $output[5]);
        $this->assertEquals( ["class" => "Returny", "line" => 14], $output[6]);
        $this->assertEquals( ["class" => 'DOMElement', "line" => 14], $output[7]);
        $this->assertEquals( ["class" => "\Exception", "line" => 14], $output[8]);
        $this->assertEquals( ["class" => "User", "line" => 26], $output[9]);
        $this->assertEquals( ["class" => "Test", "line" => 31], $output[10]);
        $this->assertEquals( ["class" => "Products", "line" => 36], $output[11]);
        $this->assertEquals( ["class" => "Product", "line" => 36], $output[12]);
        $this->assertEquals( ["class" => "Collection", "line" => 36], $output[13]);
        $this->assertEquals( ["class" => "User", "line" => 36], $output[14]);
        $this->assertEquals( ["class" => "Collection", "line" => 36], $output[15]);
        $this->assertEquals( ["class" => "Test", "line" => 36], $output[16]);
        $this->assertEquals( ["class" => "User", "line" => 36], $output[17]);
        $this->assertEquals( ["class" => "Empty", "line" => 43], $output[18]);
        $this->assertEquals( ["class" => "MixArray", "line" => 43], $output[19]);
        $this->assertEquals( ["class" => "User", "line" => 43], $output[20]);
    }

    /** @test */
    public function can_detect_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/class_references.stub');
        [$classRefs, $attributeRefs, $namespace] = ClassReferenceFinder::process($tokens);

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
    public function can_detect_inline_class_references()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/inline_class_references.stub');
        [$classRefs, $attributeRefs, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, '\A', 9]], $classRefs[0]);
        $this->assertEquals([[T_STRING, '\InterF1', 9]], $classRefs[1]);
        $this->assertEquals([[T_STRING, '\InterF2', 9]], $classRefs[2]);
        $this->assertEquals([[T_STRING, '\B', 9]], $classRefs[3]);

        $this->assertEquals([[T_STRING, '\Trait1', 11]], $classRefs[4]);
        $this->assertEquals([[T_STRING, '\Trait2', 11]], $classRefs[5]);
        $this->assertEquals([[T_STRING, '\Trait3', 13]], $classRefs[6]);

        $this->assertEquals([[T_STRING, '\TypeHint1', 17]], $classRefs[7]);
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
        [$classRefs, $attributeRefs,] = ClassReferenceFinder::process($tokens);
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
}
