<?php

namespace Imanghafoori\TokenAnalyzer\Tests\Fakes;

class ExistenceChecker
{
    public static function check($import, $absFilePath): bool
    {
        return false;
    }
}