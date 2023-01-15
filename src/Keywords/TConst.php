<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TConst
{
    public static function is($token)
    {
        return $token === T_CONST;
    }

    public static function body(ClassRefProperties $properties, &$tokens)
    {
        $i = key($tokens);
        while (in_array($tokens[$i + 1][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
            $i++;
        }
        is_int($tokens[$i + 1][0]) && $tokens[$i + 1][0] = T_STRING;
        unset($i);
    }
}