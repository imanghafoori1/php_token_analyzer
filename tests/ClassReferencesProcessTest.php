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
        $this->assertEquals(1, count($output));
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
        $this->assertEquals(3, count($output));
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
        $this->assertEquals("H", $output[3][0][1]);
        $this->assertEquals("T", $output[4][0][1]);
        $this->assertEquals("", $namespace);
    }

    /** @test */
    public function can_detect_class_references()
    {
        $string = file_get_contents(__DIR__.'/stubs/class_references.stub');
        $tokens = token_get_all($string);

        [$output, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals([[T_STRING, 'InterF1', 9]], $output[1]);
        $this->assertEquals([[T_STRING, 'InterF2', 9]], $output[2]);

        $this->assertEquals([[T_STRING, 'Trait1', 11]], $output[4]);
        $this->assertEquals([[T_STRING, 'Trait2', 11]], $output[5]);
        $this->assertEquals([[T_STRING, 'Trait3', 13]], $output[6]);

        $this->assertEquals([[T_STRING, 'TypeHint1', 17]], $output[7]);
        $this->assertEquals([[T_STRING, 'TypeHint2', 17]], $output[8]);
        $this->assertEquals([[T_STRING, 'Finder', 23]], $output[9]);
        $this->assertEquals([[T_STRING, 'DirectoryNotFoundException', 31]], $output[10]);
        $this->assertEquals([[T_STRING, 'Finder', 36]], $output[11]);
        $this->assertEquals([[T_STRING, 'MyAmIClass', 41]], $output[14]);
        $this->assertEquals([[T_STRING, 'TypeHint1', 51]], $output[17]);
        $this->assertEquals([[T_STRING, 'ReturnyType2', 51]], $output[18]);
        $this->assertEquals([[T_STRING, 'Newed', 59]], $output[20]);

        $this->assertEquals("Imanghafoori\LaravelMicroscope\FileReaders", $namespace);
        $this->assertEquals([[T_STRING, 'InConstructor', 67]], $output[22]);

        $this->assertEquals([[T_STRING, 'F', 72]], $output[24]);

        if (version_compare(phpversion(), '8.0.0') !== 1) {
            $this->assertEquals([[T_NS_SEPARATOR, '\\', 9], [T_STRING, 'Inline', 9], [T_NS_SEPARATOR, '\\', 9], [T_STRING, 'InterF3', 9]], $output[3]);

            $this->assertEquals([[T_NS_SEPARATOR, '\\', 9], [T_STRING, 'A', 9], [T_NS_SEPARATOR, '\\', 9], [T_STRING, 'ParentClass', 9]], $output[0]);

            $this->assertEquals([[T_NS_SEPARATOR, '\\', 37], [T_STRING, 'Exception', 37]], $output[12]);
            $this->assertEquals([[T_NS_SEPARATOR, '\\', 37], [T_STRING, 'ErrorException', 37]], $output[13]);
            $this->assertEquals([[T_NS_SEPARATOR, '\\', 42], [T_STRING, 'YetAnotherclass', 42]], $output[15]);
            $this->assertEquals([[T_STRING, 'HalfImported', 43], [T_NS_SEPARATOR, '\\', 43], [T_STRING, 'TheRest', 43]], $output[16]);
            $this->assertEquals([[T_STRING, 'A', 60], [T_NS_SEPARATOR, '\\', 60], [T_STRING, 'Newed', 60]], $output[21]);
            $this->assertEquals([
                [T_NS_SEPARATOR, '\\', 70],
                [T_STRING, 'A', 70],
                [T_NS_SEPARATOR, '\\', 70],
                [T_STRING, 'ReturnType', 70],
            ], $output[24]);
        }
    }
}
