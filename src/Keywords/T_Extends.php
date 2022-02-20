<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class T_Extends
{
    public static function is($token, $namespace = null)
    {
        return $token === T_EXTENDS;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect)
    {
        $collect = true;
        //isset($classes[$c]) && $c++;
        ClassReferenceFinder::forward();

        return true;
    }
}