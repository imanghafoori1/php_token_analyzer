<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class ClassReferencesProcessTest extends BaseTestClass
{
    /** @test */
    public function traits()
    {
        $string = file_get_contents(__DIR__.'/stubs/used_trait.stub');
        $tokens = token_get_all($string);

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
    public function can_instanceof()
    {
        $string = file_get_contents(__DIR__.'/stubs/instanceof.stub');
        $tokens = token_get_all($string);

        [$output, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('Hello', $output[0][0][1]);
        $this->assertEquals('Hello2', $output[1][0][1]);
        $this->assertEquals('\Hello3', $output[2][0][1]);
        $this->assertCount(3, $output);
    }

    /** @test */
    public function refs_in_flat_files()
    {
        $string = file_get_contents(__DIR__.'/stubs/non_in_class_refs.stub');
        $tokens = token_get_all($string);

        [$output, $namespace] = ClassReferenceFinder::process($tokens);
        $this->assertEquals("Model\User", $output[0][0][1]);
        $this->assertEquals("H", $output[1][0][1]);
        $this->assertEquals("T", $output[2][0][1]);
        $this->assertEquals("", $namespace);
    }

    /** @test */
    public function can_detect_docblocks()
    {
        $string = file_get_contents(__DIR__.'/stubs/doc_block_ref.stub');
        $tokens = token_get_all($string);

        $output = ClassReferenceFinder::readRefsInDocblocks($tokens);
        $this->assertEquals( ["class" => "A", "line" => 5], $output[0]);
        $this->assertEquals( ["class" => "Logger", "line" => 9], $output[1]);
        $this->assertEquals( ["class" => "Hello", "line" => 14], $output[2]);
        $this->assertEquals( ["class" => "Hello3", "line" => 14], $output[3]);
        $this->assertEquals( ["class" => "Hello2", "line" => 14], $output[4]);
        $this->assertEquals( ["class" => "ArrayIterator", "line" => 14], $output[5]);
        $this->assertEquals( ["class" => "Returny", "line" => 14], $output[6]);
        $this->assertEquals( ["class" => '$this', "line" => 14], $output[7]);
        $this->assertEquals( ["class" => "DOMElement", "line" => 14], $output[8]);
        $this->assertEquals( ["class" => "\Exception", "line" => 14], $output[9]);
    }

    /** @test */
    public function can_detect_class_references()
    {
        $string = file_get_contents(__DIR__.'/stubs/class_references.stub');
        $tokens = token_get_all($string);

        [$output, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, 'A', 9]], $output[0]);
        $this->assertEquals([[T_STRING, 'InterF1', 9]], $output[1]);
        $this->assertEquals([[T_STRING, 'InterF2', 9]], $output[2]);
        $this->assertEquals([[T_STRING, 'B', 9]], $output[3]);

        $this->assertEquals([[T_STRING, 'Trait1', 11]], $output[4]);
        $this->assertEquals([[T_STRING, 'Trait2', 11]], $output[5]);
        $this->assertEquals([[T_STRING, 'Trait3', 13]], $output[6]);

        $this->assertEquals([[T_STRING, 'TypeHint1', 17]], $output[7]);
        $this->assertEquals([[T_STRING, 'TypeHint2', 17]], $output[8]);
        $this->assertEquals([[T_STRING, 'Finder', 23]], $output[9]);
        $this->assertEquals([[T_STRING, 'DirectoryNotFoundException', 31]], $output[10]);
        $this->assertEquals([[T_STRING, 'MyAmIClass', 35]], $output[11]);
        $this->assertEquals([[T_STRING, 'TypeHint1', 43]], $output[12]);
        $this->assertEquals([[T_STRING, 'ReturnyType2', 43]], $output[13]);
        $this->assertEquals([[T_STRING, 'Newed', 51]], $output[14]);
        $this->assertEquals([[T_STRING, 'Newed', 51]], $output[15]);

        $this->assertEquals("Imanghafoori\LaravelMicroscope\FileReaders", $namespace);
        $this->assertEquals([[T_STRING, 'InConstructor', 58]], $output[16]);

        $this->assertEquals([[T_STRING, 'F', 63]], $output[17]);
    }

    /** @test */
    public function can_detect_inline_class_references()
    {
        $string = file_get_contents(__DIR__.'/stubs/inline_class_references.stub');
        $tokens = token_get_all($string);

        [$output, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, '\A', 9]], $output[0]);
        $this->assertEquals([[T_STRING, '\InterF1', 9]], $output[1]);
        $this->assertEquals([[T_STRING, '\InterF2', 9]], $output[2]);
        $this->assertEquals([[T_STRING, '\B', 9]], $output[3]);

        $this->assertEquals([[T_STRING, '\Trait1', 11]], $output[4]);
        $this->assertEquals([[T_STRING, '\Trait2', 11]], $output[5]);
        $this->assertEquals([[T_STRING, '\Trait3', 13]], $output[6]);

        $this->assertEquals([[T_STRING, '\TypeHint1', 17]], $output[7]);
        $this->assertEquals([[T_STRING, '\TypeHint2', 17]], $output[8]);
        $this->assertEquals([[T_STRING, '\Finder', 23]], $output[9]);
        $this->assertEquals([[T_STRING, '\DirectoryNotFoundException', 31]], $output[10]);
        $this->assertEquals([[T_STRING, '\MyAmIClass', 35]], $output[11]);
        $this->assertEquals([[T_STRING, '\TypeHint1', 43]], $output[12]);
        $this->assertEquals([[T_STRING, '\ReturnyType2', 43]], $output[13]);
        $this->assertEquals([[T_STRING, '\Newed', 51]], $output[14]);
        $this->assertEquals([[T_STRING, '\Newed', 51]], $output[15]);

        $this->assertEquals("Imanghafoori\LaravelMicroscope\FileReaders", $namespace);
        $this->assertEquals([[T_STRING, '\InConstructor', 58]], $output[16]);

        $this->assertEquals([[T_STRING, '\F', 63]], $output[17]);
        $this->assertEquals([[T_STRING, '\iteable', 66]], $output[18]);

        $this->assertEquals([[T_STRING, '\countable', 66]], $output[19]);
        $this->assertEquals([[T_STRING, '\User', 68]], $output[20]);
        $this->assertEquals([[T_STRING, '\ParentOfAnonymous', 77]], $output[21]);
        $this->assertEquals([[T_STRING, '\interfaceOfAnonymous', 78]], $output[22]);
        $this->assertEquals([[T_STRING, '\A\interfaceOfAnonymous', 79]], $output[23]);
    }

    /** @test */
    public function namespaced_function_call()
    {
        $string = file_get_contents(__DIR__.'/stubs/namespaced_function_call.stub');
        $tokens = token_get_all($string);

        [$output, $namespace] = ClassReferenceFinder::process($tokens);
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

        $this->assertEquals($expected, $output);
    }
}
