<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ParseUseStatement;

class FindClassReferencesTest extends BaseTestClass
{
    /** @test */
    public function can_find_class_references()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/class_references.stub'));

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
}
