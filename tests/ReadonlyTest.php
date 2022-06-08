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
        $this->assertCount(2, $output);
        $this->assertEquals('Hello2', $output[0][0][1]);
        $this->assertEquals('Hello3', $output[1][0][1]);

    }
}


