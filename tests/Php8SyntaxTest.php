<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassMethods;

class Php8SyntaxTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (version_compare(phpversion(), '8.0.0') !== 1) {
            $this->markTestSkipped('Your php version is less than 8.0');
        }
    }

    /** @test */
    public function can_detect_php8_syntax()
    {
        $string = file_get_contents(__DIR__.'/stubs/php80/sample_class.stub');
        $tokens = token_get_all($string);

        $actual = ClassMethods::read($tokens);

        $expected = [
            'name' => [
                0 => T_STRING,
                1 => 'sample_class',
                2 => 5,
            ],
            'methods' => [
                0 => [
                    'name' => [T_STRING, '__construct', 7],
                    'visibility' => [T_PUBLIC, 'public', 7],
                    'signature' => [
                        [T_PRIVATE, 'private', 7],
                        [T_WHITESPACE, ' ', 7],
                        [T_STRING, 'Hello', 7],
                        [T_WHITESPACE, ' ', 7],
                        [T_VARIABLE, '$foo', 7],
                    ],
                    'body' => '',
                    'startBodyIndex' => [34, 36],
                    'returnType' => [
                        [T_STRING, 'G1', 7],
                        [T_STRING, 'G2', 7],
                        [T_STRING, 'G3', 7],
                    ],
                    'nullable_return_type' => false,
                    'is_static' => false,
                    'is_abstract' => false,
                    'is_final' => false,
                ],
            ],
            'type' => T_CLASS,
            'is_abstract' => false,
            'is_final' => false,
        ];
        $this->assertEquals($expected, $actual);
    }
}