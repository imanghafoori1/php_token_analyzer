<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TCase
{
    public static function is($token)
    {
        return $token === T_CASE;
    }

    public static function body(ClassRefProperties $properties, &$tokens)
    {
        return TConst::body($properties, $tokens);
    }
}