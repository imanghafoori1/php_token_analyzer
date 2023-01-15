<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Colon
{
    public static function is($token)
    {
        return $token === ':';
    }

    public static function body(ClassRefProperties $properties)
    {
        if ($properties->isSignature) {
            $properties->collect = true;
        } else {
            $properties->collect = false;
            isset($properties->classes[$properties->c]) && $properties->c++;
        }
        ClassReferenceFinder::forward();

        return true;
    }
}