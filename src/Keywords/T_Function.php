<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

class T_Function
{
    public static function is($token, $namespace = null)
    {
        return $token === T_FUNCTION;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod)
    {
        $isDefiningFunction = true;
        if ($isInSideClass and ! $isInsideMethod) {
            $isDefiningMethod = true;
        }
    }
}