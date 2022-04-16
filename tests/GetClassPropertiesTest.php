<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\GetClassProperties;

class GetClassPropertiesTest extends BaseTestClass
{
    /** @test */
    public function enums()
    {
        if (version_compare(phpversion(), '8.1.0') === 1) {
            [$namespace, $name, $type, $parent, $interfaces] = GetClassProperties::fromFilePath(__DIR__.'/stubs/sample_enum.stub');

            $this->assertEquals("Hello", $namespace);
            $this->assertEquals('Hi', $name);
            $this->assertEquals(T_ENUM, $type);
            $this->assertEquals('', $parent);
            $this->assertEquals('', $interfaces);
        } else {
            $this->markTestSkipped();
        }
    }

    /** @test */
    public function can_detect_method_visibility()
    {
        [$namespace, $name, $type, $parent, $interfaces] = GetClassProperties::fromFilePath(__DIR__.'/stubs/HomeController.stub');

        $this->assertEquals("App\Http\Controllers", $namespace);
        $this->assertEquals('HomeController', $name);
        $this->assertEquals(T_CLASS, $type);
        $this->assertEquals('Controller', $parent);
        $this->assertEquals('Countable|MyInterface', $interfaces);
    }

    /** @test */
    public function can_detect_multi_extend()
    {
        [$namespace, $name, $type, $parent, $interfaces] = GetClassProperties::fromFilePath(__DIR__.'/stubs/multi_extend_interface.stub');

        $this->assertEquals("App\Models\Support", $namespace);
        $this->assertEquals('BaseInterface', $name);
        $this->assertEquals(T_INTERFACE, $type);
        $this->assertEquals('AnotherBaseInterface|Arrayable|Jsonable|JsonSerializable', $parent);
    }

    /** @test */
    public function can_detect_multi_extend_1()
    {
        [$namespace, $name, $type, $parent, $interfaces] = GetClassProperties::fromFilePath(__DIR__.'/stubs/interface_sample.stub');

        $this->assertEquals('', $namespace);
        $this->assertEquals('interface_sample', $name);
        $this->assertEquals(T_INTERFACE, $type);
        $this->assertEquals('IncompleteTest', $parent);
    }

    /** @test */
    public function can_detect_simple_classes()
    {
        [$namespace, $name, $type, $parent, $interfaces] = GetClassProperties::fromFilePath(__DIR__.'/stubs/I_am_simple.stub');

        $this->assertEquals('', $namespace);
        $this->assertEquals('I_am_simple', $name);
        $this->assertEquals(T_CLASS, $type);
        $this->assertEquals('', $parent);
    }

    /** @test */
    public function non_php_file()
    {
        [$namespace, $name, $type, $parent, $interfaces] = GetClassProperties::fromFilePath(__DIR__.'/stubs/non_php_opening_tag.stub');

        $this->assertEquals(null, $namespace);
        $this->assertEquals(null, $name);
        $this->assertEquals(null, $type);
        $this->assertEquals(null, $parent);
        $this->assertEquals(null, $interfaces);
    }
}
