<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class ReadonlyTest extends BaseTestClass
{
    /** @test */
    public function readonly_properties()
    {
        $string = file_get_contents(__DIR__.'/stubs/readonly_property.stub');
        $tokens = token_get_all($string);
        [$output, $namespace] = ClassReferenceFinder::process($tokens);
        $this->assertCount(3, $output);
        $this->assertEquals('Hello1', $output[0][0][1]);
        $this->assertEquals('Hello2', $output[1][0][1]);
        $this->assertEquals('Hello3', $output[2][0][1]);
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
        $this->assertEquals('tH6', $output[6][0][1]);
        $this->assertEquals('tH7', $output[7][0][1]);
        $this->assertEquals('tH8', $output[8][0][1]);
        $this->assertEquals('tH9', $output[9][0][1]);
    }
}


