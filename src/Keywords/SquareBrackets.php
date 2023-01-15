<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class SquareBrackets
{
    public static function is($token, $namespace = null)
    {
        return $token === '[' || $token === ']';
    }

    public static function body(ClassRefProperties $properties, &$tokens, &$t)
    {
        if ($t === '[') {
            $properties->fnLevel++;
            $properties->isInsideArray++;

            return false;
        }

        $properties->fnLevel--;
        $properties->isInsideArray--;
        $properties->force_close = $properties->collect = false;
        isset($properties->classes[$properties->c]) && $properties->c++;
        ClassReferenceFinder::forward();

        return true;
    }
}