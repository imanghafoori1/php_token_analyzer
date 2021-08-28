<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class T_Implements
{
    public static function is($token, $namespace = null)
    {
        return $token === T_IMPLEMENTS;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes)
    {
        $collect = $implements = true;
        isset($classes[$c]) && $c++;
        ClassReferenceFinder::forward();

        return true;
    }
}