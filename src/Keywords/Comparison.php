<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Comparison
{
    public static function is($token)
    {
        return $token === T_IS_IDENTICAL || $token === T_IS_EQUAL;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->collect && $properties->c++;
        $properties->collect = false;
        ClassReferenceFinder::forward();

        return true;
    }
}
