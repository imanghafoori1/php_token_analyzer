<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TExtends
{
    public static function is($token)
    {
        return $token === T_EXTENDS;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->collect = true;
        //isset($classes[$c]) && $c++;
        ClassReferenceFinder::forward();

        return true;
    }
}