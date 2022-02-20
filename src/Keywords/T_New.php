<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class T_New
{
    public static function is($token, $namespace = null)
    {
        return $token === T_NEW;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect)
    {
        // We start to collect tokens after the new keyword.
        // unless we reach a variable name.
        // currently tokenizer recognizes CONST NEW = 1; as new keyword.
        (ClassReferenceFinder::$lastToken[0] != T_CONST) && $collect = true;
        ClassReferenceFinder::forward();

        // we do not want to collect the new keyword itself
        return true;
    }
}