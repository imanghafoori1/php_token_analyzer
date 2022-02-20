<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Separator
{
    public static function is($token, $namespace = null)
    {
        return $token === T_NS_SEPARATOR;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes, &$isSignature)
    {
        if (! $force_close) {
            $collect = true;
        }

        // Add the previous token,
        // In case the namespace does not start with '\'
        // like: App\User::where(...
        if (ClassReferenceFinder::$lastToken[0] === T_STRING && $collect && ! isset($classes[$c])) {
            $classes[$c][] = ClassReferenceFinder::$lastToken;
        }
    }
}