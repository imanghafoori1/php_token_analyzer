<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class DoubleColon
{
    public static function is($token, $namespace = null)
    {
        return $token === T_DOUBLE_COLON;
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes)
    {
        // When we reach the ::class syntax.
        // we do not want to treat: $var::method(), self::method()
        // as a real class name, so it must be of type T_STRING
        if (! $collect && ClassReferenceFinder::$lastToken[0] === T_STRING && ! \in_array(ClassReferenceFinder::$lastToken[1], ['parent', 'self', 'static'], true) && (ClassReferenceFinder::$secLastToken[1] ?? null) !== '->') {
            $classes[$c][] = ClassReferenceFinder::$lastToken;
        }
        $collect = false;
        isset($classes[$c]) && $c++;
        ClassReferenceFinder::forward();

        return true;
    }
}