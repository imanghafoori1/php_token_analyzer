<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

class WhiteSpaceOrCommand
{
    public static function is($token, $namespace = null)
    {
        return $token === T_WHITESPACE || $token === '&' || $token === T_COMMENT;
    }

    public static function body()
    {
        // We do not want to keep track of
        // white spaces or collect them
        return true;
    }
}