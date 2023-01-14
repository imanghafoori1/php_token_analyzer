<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class AccessModifiers
{
    public static function is($token)
    {
        return \in_array($token, [T_PUBLIC, T_PROTECTED, T_PRIVATE], true) &&
            ! \in_array(ClassReferenceFinder::$lastToken[0], [T_AS, T_CONST, T_CASE]);
    }

    public static function body(ClassRefProperties $properties, &$tokens)
    {
        $_ = next($tokens);

        if ($_[0] === T_STATIC && $_[1] === 'static') {
            while (($_ = next($tokens))[0] === T_WHITESPACE) {
            }
        }

        if ($_[0] === T_CONST || $_[0] === T_FUNCTION) {
            return true;
        }

        $properties->collect = true;
        ClassReferenceFinder::forward();
        $properties->declaringProperty = true;
        $properties->isInsideMethod = false;

        return true;
    }
}