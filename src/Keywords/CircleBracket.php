<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class CircleBracket
{
    public static function is($token, $namespace = null)
    {
        return $token === '(' || $token === ')';
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes, &$isSignature)
    {
        // wrong...
        if ($t === '(' && ($isDefiningFunction || $isCatchException)) {
            $isSignature = true;
            $collect = true;
        } else {
            // so is calling a method by: ()
            $collect = false;
        }
        if ($t === ')') {
            $isCatchException = $isDefiningFunction = false;
        }
        isset($classes[$c]) && $c++;
        ClassReferenceFinder::forward();

        return true;
    }
}