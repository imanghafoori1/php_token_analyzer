<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class T_Use
{
    public static function is($token, $namespace = null)
    {
        return $token === T_USE;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait)
    {
        next($tokens);
        // use function Name\Space\function_name;
        if (current($tokens)[0] === T_FUNCTION) {
            // we do not collect the namespaced function name
            next($tokens);
            $force_close = true;
            ClassReferenceFinder::forward();

            return true;
        }

        // function () use ($var) {...}
        // for this type of use we do not care and continue;
        // who cares?!
        if (ClassReferenceFinder::$lastToken === ')') {
            ClassReferenceFinder::forward();

            return true;
        }

        // Since we don't want to collect use statements (imports)
        // and we want to collect the used traits on the class.
        if (! $isInSideClass) {
            $force_close = true;
            $collect = false;
        } else {
            $trait = true;
            $collect = true;
        }
        ClassReferenceFinder::forward();

        return true;
    }
}