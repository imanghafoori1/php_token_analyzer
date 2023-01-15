<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TCatch
{
    public static function is($token)
    {
        return $token === T_CATCH;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->collect = true;
        $properties->isCatchException = true;

        return true;
    }
}