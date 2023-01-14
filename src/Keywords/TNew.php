<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TNew
{
    public static function is($token)
    {
        return $token === T_NEW;
    }

    public static function body(ClassRefProperties $properties)
    {
        // We start to collect tokens after the new keyword.
        // unless we reach a variable name.
        // currently, tokenizer recognizes CONST NEW = 1; as new keyword.
        // case New = 'new';
        if (! in_array(ClassReferenceFinder::$lastToken[0], [T_CONST, T_CASE, T_DOUBLE_COLON])) {
            $properties->collect = true;
            $properties->isNewing = true;
        }
        ClassReferenceFinder::forward();

        // we do not want to collect the new keyword itself
        return true;
    }
}