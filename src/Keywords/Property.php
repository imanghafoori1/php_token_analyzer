<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

class Property
{
    public static function is($token, $namespace = null)
    {
        return \in_array($token, [T_PUBLIC, T_PROTECTED, T_PRIVATE], true);
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace, &$isInsideMethod)
    {
        $isInsideMethod = false;
    }
}