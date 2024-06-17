<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\Refactor;

class FlattenTest extends BaseTestClass
{
    public function test_flatten()
    {
        $dir = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'refactor'.DIRECTORY_SEPARATOR;
        $code = file_get_contents($dir.'nested_if.stub');

        $tokens = token_get_all($code);
        [$tokens, $changes] = Refactor::flatten($tokens);

        foreach ($tokens as $i => $token) {
            if ($token[0] === T_WHITESPACE) {
                $tokens[$i][1] = ' ';
            }
        }
        $result = Refactor::toString($tokens);
        $this->assertEquals(file_get_contents($dir.'nested_if_flat.stub'). '  ', $result);
        $this->assertEquals(1, $changes);
    }

    public function test_flatten2()
    {
        $dir = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'refactor'.DIRECTORY_SEPARATOR;
        $code = file_get_contents($dir.'multi_if.stub');

        $tokens = token_get_all($code);
        [$tokens, $changes] = Refactor::flatten($tokens);

        foreach ($tokens as $i => $token) {
            if ($token[0] === T_WHITESPACE) {
                $tokens[$i][1] = ' ';
            }
        }
        $result = Refactor::toString($tokens);
        $this->assertEquals(file_get_contents($dir.'multi_if_flat.stub'). '', $result);
        $this->assertEquals(2, $changes);
    }

    public function test_flatten3()
    {
        $dir = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'refactor'.DIRECTORY_SEPARATOR;
        $code = file_get_contents($dir.'early_return_2.stub');

        $tokens = token_get_all($code);
        [$tokens, $changes] = Refactor::flatten($tokens);

        foreach ($tokens as $i => $token) {
            if ($token[0] === T_WHITESPACE) {
                $tokens[$i][1] = ' ';
            }
        }
        $result = Refactor::toString($tokens);
        $this->assertEquals(file_get_contents($dir.'early_return_2_flat.stub'). '  ', $result);
        $this->assertEquals(1, $changes);
    }

    public function test_flatten4()
    {
        $dir = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'refactor'.DIRECTORY_SEPARATOR;
        $code = file_get_contents($dir.'early_return_3.stub');

        $tokens = token_get_all($code);
        [$tokens, $changes] = Refactor::flatten($tokens);

        foreach ($tokens as $i => $token) {
            if ($token[0] === T_WHITESPACE) {
                $tokens[$i][1] = ' ';
            }
        }

        $result = Refactor::toString($tokens);
        $this->assertEquals(file_get_contents($dir.'early_return_3_flat.stub'), $result);
        $this->assertEquals(1, $changes);
    }
}