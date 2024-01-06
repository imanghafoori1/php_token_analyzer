<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TNamespace
{
    public static function is($token, $namespace = null)
    {
        $lastToken = ClassReferenceFinder::$lastToken[0];

        return $token === T_NAMESPACE && ! $namespace && $lastToken !== T_DOUBLE_COLON && $lastToken !== T_OBJECT_OPERATOR;
    }

    public static function body(ClassRefProperties $properties, &$tokens)
    {
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
