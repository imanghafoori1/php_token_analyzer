<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassMethods;

class Php82SyntaxTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (version_compare(phpversion(), '8.2.0') !== 1) {
            $this->markTestSkipped('Your php version is less than 8.2');
        }
    }

    /** @test */
    public function check_class_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens('/stubs/sample_class_php82.stub'));
        $methods = $class['methods'];

        $this->assertEquals(3, count($methods));

        $this->assertTrue($methods[0]['nullable_return_type']);
        $this->assertFalse($methods[1]['nullable_return_type']);
        $this->assertFalse($methods[2]['nullable_return_type']);

        $this->assertEquals('null', $methods[0]['returnType'][0][1]);
        $this->assertEquals('true', $methods[1]['returnType'][0][1]);
        $this->assertEquals('false', $methods[2]['returnType'][0][1]);
    }

    /** @test */
    public function check_abstract_class_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens('/stubs/abstract_sample_class_php82.stub'));
        $methods = $class['methods'];

        $this->assertTrue($methods[0]['nullable_return_type']);

        $this->assertEquals('null', $methods[0]['returnType'][0][1]);
        $this->assertEquals('true', $methods[1]['returnType'][0][1]);
        $this->assertEquals('false', $methods[2]['returnType'][0][1]);

        $this->assertCount(3, $methods);
    }

    /** @test */
    public function check_interface_return_types_test()
    {
        $class = ClassMethods::read($this->getTokens('/stubs/interface_sample_php82.stub'));
        $methods = $class['methods'];

        $this->assertTrue($methods[0]['nullable_return_type']);

        $this->assertEquals('null', $methods[0]['returnType'][0][1]);
        $this->assertEquals('true', $methods[1]['returnType'][0][1]);
        $this->assertEquals('false', $methods[2]['returnType'][0][1]);
    }
}