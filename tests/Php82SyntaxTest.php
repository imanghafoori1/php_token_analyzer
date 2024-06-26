<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassMethods;

class Php82SyntaxTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (! version_compare(phpversion(), '8.1.99', '>=')) {
            $this->markTestSkipped('Skipping, your php version is less than 8.2');
        }
    }

    public function test_check_class_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens(__DIR__.'/stubs/php82/sample_class.stub'));
        $methods = $class['methods'];

        $this->assertCount(3, $methods);

        $this->assertTrue($methods[0]['nullable_return_type']);
        $this->assertFalse($methods[1]['nullable_return_type']);
        $this->assertFalse($methods[2]['nullable_return_type']);

        $this->assertEquals('null', $methods[0]['returnType'][0][1]);
        $this->assertEquals('true', $methods[1]['returnType'][0][1]);
        $this->assertEquals('false', $methods[2]['returnType'][0][1]);
    }

    public function test_check_abstract_class_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens(__DIR__.'/stubs/php82/abstract_sample_class.stub'));
        $methods = $class['methods'];

        $this->assertTrue($methods[0]['nullable_return_type']);

        $this->assertEquals('null', $methods[0]['returnType'][0][1]);
        $this->assertEquals('true', $methods[1]['returnType'][0][1]);
        $this->assertEquals('false', $methods[2]['returnType'][0][1]);

        $this->assertCount(3, $methods);
    }

    public function test_check_interface_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens(__DIR__.'/stubs/php82/interface_sample.stub'));
        $methods = $class['methods'];

        $this->assertTrue($methods[0]['nullable_return_type']);

        $this->assertEquals('null', $methods[0]['returnType'][0][1]);
        $this->assertEquals('true', $methods[1]['returnType'][0][1]);
        $this->assertEquals('false', $methods[2]['returnType'][0][1]);
    }
}
