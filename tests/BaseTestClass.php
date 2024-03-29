<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use PHPUnit\Framework\TestCase;

class BaseTestClass extends TestCase
{
    /**
     * get tokens of stubs classes.
     *
     * @param string $path path of stub file
     *
     * @return array
     */
    protected function getTokens(string $path): array
    {
        return token_get_all(file_get_contents($path));
    }
}
