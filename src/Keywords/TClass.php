<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TClass
{
    public static function is($token)
    {
        return $token === T_CLASS;
    }

    public static function body(ClassRefProperties $properties)
    {
        // new class {... }
        // ::class
        $type = ClassReferenceFinder::$lastToken[0];
        if ($type === T_NEW || $type === T_DOUBLE_COLON) {
            $properties->collect = false;
            ClassReferenceFinder::forward();

            return true;
        }
        $properties->isInSideClass = true;
    }
}
