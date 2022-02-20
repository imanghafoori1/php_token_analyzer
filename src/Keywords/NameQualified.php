<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

class NameQualified
{
    public static function is($token, $namespace = null)
    {
        return $token === T_NAME_QUALIFIED || $token === T_NAME_FULLY_QUALIFIED;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect)
    {
        if ($isInSideClass) {
            $collect = true;
        }
        //self::forward();
    }
}