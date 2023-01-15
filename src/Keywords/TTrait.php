<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TTrait
{
    public static function is($token)
    {
        return $token === T_TRAIT;
    }

    public static function body(ClassRefProperties $properties)
    {
        // new class {... }
        // ::class
        if (ClassReferenceFinder::$lastToken[0] === T_NEW || ClassReferenceFinder::$lastToken[0] === T_DOUBLE_COLON) {
            $properties->collect = false;
            ClassReferenceFinder::forward();

            return true;
        }
        $properties->isInSideClass = true;
    }
}