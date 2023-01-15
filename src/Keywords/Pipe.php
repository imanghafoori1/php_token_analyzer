<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Pipe
{
    public static function is($token)
    {
        return $token === '|' || $token === T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG;
    }

    public static function body(ClassRefProperties $properties) {
        isset($properties->classes[$properties->c]) && $properties->c++;
        ClassReferenceFinder::forward();

        return true;
    }
}