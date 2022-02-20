<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Variable
{
    public static function is($token, $namespace = null)
    {
        return $token === T_VARIABLE || $token === T_ELLIPSIS;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect)
    {
        //if ($isDefiningFunction) {
        //$c++;
        //}
        $collect = false;
        ClassReferenceFinder::forward();
        // we do not want to collect variables
        return true;
    }
}