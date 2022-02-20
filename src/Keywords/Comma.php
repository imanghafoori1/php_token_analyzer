<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Comma
{
    public static function is($token, $namespace = null)
    {
        return $token === ',';
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes, &$isSignature)
    {
        // to avoid mistaking commas in default array values with commas between args
        // example:   function hello($arg = [1, 2]) { ... }
        $collect = ($isSignature && ClassReferenceFinder::$lastToken[0] === T_VARIABLE) || $implements || $trait;
        $isInSideClass && ($force_close = false);
        // for method calls: foo(new Hello, $var);
        // we do not want to collect after comma.
        isset($classes[$c]) && $c++;
        ClassReferenceFinder::forward();

        return true;
    }
}