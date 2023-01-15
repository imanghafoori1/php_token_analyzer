<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

class Whitespace
{
    public static function is($token)
    {
        return $token === T_WHITESPACE || $token === '&' || $token === T_COMMENT || $token === T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG;
    }

    public static function body()
    {
        // We do not want to keep track of white spaces or collect them
        return true;
    }
}