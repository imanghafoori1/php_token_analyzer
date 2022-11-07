<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class TypeHintedPropertiesTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (! version_compare(phpversion(), '7.4.0', '>=')) {
            $this->markTestSkipped('Your php version is less than 7.4');
        }
    }

    /** @test */
    public function type_hinted_property()
    {
        $string = file_get_contents(__DIR__.'/stubs/type_hinted_property.stub');
        $tokens = token_get_all($string);
        [$output, $namespace] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('', $namespace);
        $this->assertEquals('tH0', $output[0][0][1]);
        $this->assertEquals('tH1', $output[1][0][1]);
        $this->assertEquals('t\H2', $output[2][0][1]);
        $this->assertEquals('\tH3', $output[3][0][1]);
        $this->assertEquals('tH4', $output[4][0][1]);
        $this->assertEquals('tH5', $output[5][0][1]);
        $this->assertEquals('\tH7\tH8', $output[6][0][1]);
        $this->assertEquals('tH9', $output[7][0][1]);
    }

    /** @test */
    public function can_detect_arrow_functions_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/php74/arrow_functions.stubs'));
        [$output,] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('T4', $output[0][0][1]);
        $this->assertEquals('T5', $output[1][0][1]);
        $this->assertEquals('T6', $output[2][0][1]);
        $this->assertEquals('T7', $output[3][0][1]);
        $this->assertEquals("H", $output[4][0][1]);
        $this->assertEquals("T", $output[5][0][1]);
    }
}


