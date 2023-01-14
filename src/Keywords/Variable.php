<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Variable
{
    public static function is($token)
    {
        return $token === T_VARIABLE || $token === T_ELLIPSIS;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->collect && isset($properties->classes[$properties->c]) && $properties->c++;
        $properties->collect = false;
        ClassReferenceFinder::forward();
        // we do not want to collect variables
        return true;
    }
}