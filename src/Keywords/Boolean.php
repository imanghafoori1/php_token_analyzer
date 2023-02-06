<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Boolean
{
    public static function is($token)
    {
        return in_array($token, [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_OR, T_LOGICAL_AND], true);
    }

    public static function body(ClassRefProperties $properties, &$tokens, &$token)
    {
        return Semicolon::body($properties, $tokens, $token);
    }
}
