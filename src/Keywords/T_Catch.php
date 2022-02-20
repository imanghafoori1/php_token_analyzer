<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

class T_Catch
{
    public static function is($token, $namespace = null)
    {
        return $token === T_CATCH;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException)
    {
        $collect = true;
        $isCatchException = true;

        return true;
    }
}