<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Colon
{
    public static function is($token, $namespace = null)
    {
        return $token === ':';
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes, &$isSignature)
    {
        if ($isSignature) {
            $collect = true;
        } else {
            $collect = false;
            isset($classes[$c]) && $c++;
        }
        ClassReferenceFinder::forward();

        return true;
    }
}