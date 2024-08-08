<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TNamespace
{
    public static function is($token)
    {
        return $token === T_NAMESPACE;
    }

    public static function body(ClassRefProperties $properties, &$tokens)
    {
        $previousToken = ClassReferenceFinder::$lastToken[0];

        if ($previousToken === T_DOUBLE_COLON || $previousToken === T_OBJECT_OPERATOR || $previousToken === T_FUNCTION) {
            return true;
        }

        $properties->collect = false;
        next($tokens);
        while (current($tokens)[0] !== ';') {
            (! in_array(current($tokens)[0], [T_COMMENT, T_WHITESPACE, T_DOC_COMMENT])) && $properties->namespace .= current($tokens)[1];
            next($tokens);
        }
        next($tokens);

        return true;
    }
}
