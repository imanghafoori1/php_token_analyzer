<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class NameQualified
{
    public static function is($token)
    {
        return $token === T_NAME_QUALIFIED || $token === T_NAME_FULLY_QUALIFIED;
    }

    public static function body(ClassRefProperties $properties)
    {
        if (! $properties->isImporting) {
            $properties->classes[$properties->c++][] = ClassReferenceFinder::$token;
            $properties->collect = false;
            ClassReferenceFinder::forward();

            return true;
        }
        //self::forward();
    }
}