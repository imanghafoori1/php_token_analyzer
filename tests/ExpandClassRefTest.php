<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ParseUseStatement;

class ExpandClassRefTest extends BaseTestClass
{
    public function test_can_extract_imports()
    {
        $tokens =  $this->getTokens(__DIR__.'/stubs/simple_refs.stub');

        $result = ParseUseStatement::getExpandedRef($tokens,'R2');
        $this->assertEquals('R1\R2', $result);

        $result = ParseUseStatement::getExpandedRef($tokens,'NoUse');
        $this->assertEquals('A1\A2\NoUse', $result);

        $result = ParseUseStatement::getExpandedRef($tokens,'Imported\Rest');
        $this->assertEquals('half\Imported\Rest', $result);

    }
}
