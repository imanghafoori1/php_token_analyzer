<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TAttribute
{
    public static function is($token)
    {
        return $token === T_ATTRIBUTE;
    }

    public static function body(ClassRefProperties $properties, &$tokens, &$t)
    {
        $properties->isAttribute = $properties->collect = true;

        ClassReferenceFinder::forward();

        return true;
    }
}
