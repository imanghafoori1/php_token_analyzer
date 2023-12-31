<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class InstanceOfTest extends BaseTestClass
{
    /** @test */
    public function instance_of()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/instance_of.stub');
        [$output, $namespace] = ClassReferenceFinder::process($tokens);

        $expected = [
            [[T_STRING, 'User', 3,]],
            [[T_STRING, 'App\User', 4]],
            [[T_STRING, '\App\User', 5]],
        ];
        $this->assertEquals($expected, $output);
    }
}
