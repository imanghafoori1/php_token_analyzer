<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TImplements
{
    public static function is($token)
    {
        return $token === T_IMPLEMENTS;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->collect = $properties->implements = true;
        isset($properties->classes[$properties->c]) && $properties->c++;
        ClassReferenceFinder::forward();

        return true;
    }
}