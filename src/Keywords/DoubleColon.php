<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class DoubleColon
{
    public static function is($token)
    {
        return $token === T_DOUBLE_COLON;
    }

    public static function body(ClassRefProperties $properties)
    {
        // When we reach the ::class syntax.
        // we do not want to treat: $var::method(), self::method()
        // as a real class name, so it must be of type T_STRING
        if (! $properties->collect && self::isCollecatble() &&
            ! in_array(ClassReferenceFinder::$lastToken[1], ['parent', 'self', 'static'], true) &&
            (ClassReferenceFinder::$secLastToken[1] ?? null) !== '->') {
            isset($properties->classes[$properties->c]) && $properties->c++;
            $properties->addRef(ClassReferenceFinder::$lastToken);
        }
        $properties->collect = false;
        isset($properties->classes[$properties->c]) && $properties->c++;
        ClassReferenceFinder::forward();

        return true;
    }

    private static function isCollecatble(): bool
    {
        $tokenType = ClassReferenceFinder::$lastToken[0];

        return $tokenType === T_STRING || $tokenType === T_NAME_QUALIFIED || $tokenType === T_NAME_FULLY_QUALIFIED;
    }
}
