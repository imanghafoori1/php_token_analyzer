<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class T_Namespace
{
    public static function is($token, $namespace = null)
    {
        return $token === T_NAMESPACE && ! $namespace && ClassReferenceFinder::$lastToken[0] !== T_DOUBLE_COLON;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace)
    {
        $collect = false;
        next($tokens);
        while (current($tokens)[0] !== ';') {
            (! in_array(current($tokens)[0], [T_COMMENT, T_WHITESPACE])) && $namespace .= current($tokens)[1];
            next($tokens);
        }
        next($tokens);

        return true;
    }
}