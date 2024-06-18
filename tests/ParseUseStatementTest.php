<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ImportsAnalyzer;
use Imanghafoori\TokenAnalyzer\ParseUseStatement;
use Imanghafoori\TokenAnalyzer\Tests\Fakes\ExistenceChecker;

class ParseUseStatementTest extends BaseTestClass
{
    public function test_can_extract_imports()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/interface_sample.stub');
        [$result, $uses] = ParseUseStatement::parseUseStatements($tokens);

        $expected = [
            'IncompleteTest' => ["PHPUnit\Framework\IncompleteTest", 3],
            'Countable' => ['Countable', 4],
        ];

        $this->assertEquals($expected, $uses);
        $this->assertEquals($expected, $result['interface_sample']);


        ImportsAnalyzer::$existenceChecker = ExistenceChecker::class;
        $uses = $result ?: [$uses];
        $actual = ImportsAnalyzer::getWrongRefs($tokens, __DIR__.'/stubs/interface_sample.stub', $uses);

        $expected = [
            '',
            [
                'Countable' => [
                    'Countable',
                    4,
                ],
            ],
            [],
            [
                [
                    'class' => 'PHPUnit\Framework\IncompleteTest',
                    'line' => 9,
                ],
                [
                    'class' => 'test',
                    'line' => 27,
                ],
            ],
            [],
        ];
        $this->assertEquals($expected, $actual);
    }

    public function test_can_detect_group_imports()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/group_import.stub');
        [$result, $uses] = ParseUseStatement::parseUseStatements($tokens);

        $expected = [
            'DirectoryNotFoundException' => ["Symfony\Component\Finder\Exception\DirectoryNotFoundException", 6],
            'Action' => ["Imanghafoori\LaravelMicroscope\Checks\ActionsComments", 5],
            "Hi" => ["Symfony\Component\Finder\Symfony\Component\Finder\Hello", 6],
            'Finder' => ["Symfony\Component\Finder\Symfony\Component\Finder\Finder", 6],
            'Closure' => ['Closure', 12],
            'PasswordBroker' => ["Illuminate\Contracts\Auth\PasswordBroker", 11],
            'HalfImported' =>  ["Illuminate\Contracts\HalfImported", 13],

        ];

        $this->assertEquals($expected, $uses);
    }

    public function test_can_detect_comma_seperated_imports()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/comma_seperated_imports.stub');
        [$result, $uses] = ParseUseStatement::parseUseStatements($tokens);

        $expected = [
            'A' => ['A', 3],
            'B' => ['B\B\B', 3],
            'C' => ['C', 3],
            'D' => ['D', 4],
        ];

        $this->assertEquals($expected, $uses);
    }

    public function test_can_skip_imported_global_functions()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/auth.stub');
        [$result, $uses] = ParseUseStatement::parseUseStatements($tokens);

        $this->assertEquals([], $uses);
        $this->assertEquals([], $result);

        $this->assertEquals([[], '', []], ClassReferenceFinder::process($tokens));
    }

    public function test_public_enumeration()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/issue_19.stub');
        $refs = ClassReferenceFinder::process($tokens)[0];

        $this->assertEquals([T_STRING, "MyClass", 3], $refs[0][0]);
        $this->assertCount(1, $refs);
    }
}
