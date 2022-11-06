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
    public function can_detect_class_general_test()
    {
        $class = ClassMethods::read($this->getTokens('/stubs/php80/sample_class.stub'));

        $this->assertEquals([T_STRING, 'sample_class', 5], $class['name']);
        $this->assertEquals(T_CLASS, $class['type']);
        $this->assertFalse($class['is_abstract']);
        $this->assertFalse($class['is_final']);
        $this->assertCount(1, $class['methods']);
    }

    /** @test */
    public function can_detect_class_methods_test()
    {
        $class = ClassMethods::read($this->getTokens('/stubs/php80/sample_class.stub'));
        $methods = $class['methods'];

        $this->assertEquals([T_STRING, '__construct', 7], $methods[0]['name']);
        $this->assertEquals([T_PUBLIC, 'public', 7], $methods[0]['visibility']);
        $this->assertEquals('', $methods[0]['body']);
        $this->assertFalse($methods[0]['is_final']);
        $this->assertFalse($methods[0]['is_abstract']);
        $this->assertFalse($methods[0]['is_static']);
        $this->assertFalse($methods[0]['nullable_return_type']);
    }

    /** @test */
    public function can_detect_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens('/stubs/php80/sample_class.stub'));
        $methods = $class['methods'];

        $this->assertEquals('G1', $methods[0]['returnType'][0][1]);
        $this->assertEquals('G2', $methods[0]['returnType'][1][1]);
        $this->assertEquals('G3', $methods[0]['returnType'][2][1]);
    }

    /** @test */
    public function can_detect_methods_signature_test()
    {
        $class = ClassMethods::read($this->getTokens('/stubs/php80/sample_class.stub'));
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
}