<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassMethods;

class ClassMethodsTest extends BaseTestClass
{
    /** @test */
    public function can_detect_method_visibility()
    {
        $string = file_get_contents(__DIR__.'/stubs/sample_class.stub');
        $tokens = token_get_all($string);

        $class = ClassMethods::read($tokens);

        $this->assertEquals(false, $class['is_abstract']);
        $this->assertEquals(T_CLASS, $class['type']);
        $this->assertCount(8, $class['methods']);
        $this->assertEquals(false, $class['methods'][0]['is_abstract']);
        $this->assertEquals(false, $class['methods'][0]['is_static']);
        $this->assertEquals(null, $class['methods'][0]['returnType']);
        $this->assertEquals(null, $class['methods'][0]['nullable_return_type']);
        $this->assertEquals([], $class['methods'][0]['signature']);
        $this->assertEquals([T_STRING, 'hello1', 5], $class['methods'][0]['name']);

        $this->assertEquals('public', $class['methods'][0]['visibility'][1]);
        $this->assertEquals('protected', $class['methods'][1]['visibility'][1]);
        $this->assertEquals('private', $class['methods'][2]['visibility'][1]);
        $this->assertEquals('public', $class['methods'][3]['visibility'][1]);

        $this->assertEquals('public', $class['methods'][4]['visibility'][1]);
        $this->assertEquals('protected', $class['methods'][5]['visibility'][1]);
        $this->assertEquals('private', $class['methods'][6]['visibility'][1]);
        $this->assertEquals('public', $class['methods'][7]['visibility'][1]);

        $this->assertEquals(true, $class['methods'][4]['is_static']);
        $this->assertEquals(true, $class['methods'][5]['is_static']);
        $this->assertEquals(true, $class['methods'][6]['is_static']);
        $this->assertEquals(true, $class['methods'][7]['is_static']);
    }

    /** @test */
    public function can_detect_method_visibility_on_interfaces()
    {
        $string = file_get_contents(__DIR__.'/stubs/PasswordBroker.stub');
        $tokens = token_get_all($string);

        $class = ClassMethods::read($tokens);

        $this->assertCount(2, $class['methods']);
        $this->assertEquals(T_INTERFACE, $class['type']);
        $this->assertEquals('public', $class['methods'][0]['visibility'][1]);
        $this->assertEquals('public', $class['methods'][1]['visibility'][1]);
    }

    /** @test */
    public function can_detect_methods_on_traits()
    {
        $string = file_get_contents(__DIR__.'/stubs/sample_trait.stub');
        $tokens = token_get_all($string);

        $trait = ClassMethods::read($tokens);

        $this->assertCount(6, $trait['methods']);
        $this->assertEquals(T_TRAIT, $trait['type']);
        //check visibility
        $this->assertEquals('public', $trait['methods'][0]['visibility'][1]);
        $this->assertEquals('public', $trait['methods'][1]['visibility'][1]);
        //check return type
        $this->assertEquals('string', $trait['methods'][5]['returnType'][0][1]);
        $this->assertStringContainsString('return $this->rememberTokenName;', $trait['methods'][5]['body']);
    }

    /** @test */
    public function can_detect_php8_syntax()
    {
        if (version_compare(phpversion(), '8.0.0') === 1) {
            $string = file_get_contents(__DIR__.'/stubs/php8syntax.stub');
            $tokens = token_get_all($string);

            $actual = ClassMethods::read($tokens);

            $expected = [
                'name' => [
                    0 => T_STRING,
                    1 => 'php8syntax',
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
        } else {
            $this->assertTrue(true);
        }
    }
}
