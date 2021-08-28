<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class ClassOrTrait
{
    public static function is($token, $namespace = null)
    {
        return $token === T_CLASS || $token === T_TRAIT;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect)
    {
        if (ClassReferenceFinder::$lastToken[0] === T_NEW || ClassReferenceFinder::$lastToken[0] === T_DOUBLE_COLON) {
            $collect = false;
            ClassReferenceFinder::forward();

            return true;
        }
        $isInSideClass = true;
    }
}