<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Boolean
{
    public static function is($token, $namespace = null)
    {
        return in_array($token, [';', '}', T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_OR, T_LOGICAL_AND], true);
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes, &$isSignature)
    {
        $trait = $force_close = false;

        // Interface methods end up with ";"
        $t === ';' && $isSignature = false;
        $collect && isset($classes[$c]) && $c++;
        $collect = false;

        ClassReferenceFinder::forward();

        return true;
    }
}