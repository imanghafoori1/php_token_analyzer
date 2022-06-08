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
            'class' => '\A\ParentClass',
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
            'class' => '\Inline\InterF3',
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
            'class' => "Symfony\Component\Finder\Symfony\Component\Finder\Finder",
            'line' => 36,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "\Exception",
            'line' => 37,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "\ErrorException",
            'line' => 37,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "Imanghafoori\LaravelMicroscope\FileReaders\MyAmIClass",
            'line' => 41,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => "\YetAnotherclass",
            'line' => 42,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Illuminate\Contracts\HalfImported\TheRest',
            'line' => 43,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\TypeHint1',
            'line' => 51,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\ReturnyType2',
            'line' => 51,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Newed',
            'line' => 59,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Newed',
            'line' => 59,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\A\Newed',
            'line' => 60,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\InConstructor',
            'line' => 67,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\A\ReturnType',
            'line' => 70,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\F',
            'line' => 72,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\a\a',
            'line' => 73,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\b\b',
            'line' => 73,
        ], $classes[$h++]);

       $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\iteable',
            'line' => 76,
        ], $classes[$h++]);

       $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\countable',
            'line' => 76,
        ], $classes[$h++]);


       $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\Y',
            'line' => 76,
        ], $classes[$h++]);

       $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\R',
            'line' => 76,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\User',
            'line' => 78,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\ParentOfAnonymous',
            'line' => 87,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\interfaceOfAnonymous',
            'line' => 88,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\ParentOfAnonymous',
            'line' => 89,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => 'Imanghafoori\LaravelMicroscope\FileReaders\interfaceOfAnonymous',
            'line' => 89,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\T',
            'line' => 90,
        ], $classes[$h++]);

        $this->assertEquals([
            'class' => '\interfaceOfAnonymous',
            'line' => 92,
        ], $classes[$h++]);
    }
}
