<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class SquareBrackets
{
    private static $isDefiningMethodBeforeStart = false;
    private static $isDefiningFunctionBeforeStart = false;
    private static $insideArrayBeforeStart = 0;

    public static function is($token)
    {
        return $token === '[' || $token === ']';
    }

    public static function body(ClassRefProperties $properties, &$tokens, &$t)
    {
        if ($t === '[') {
            if ($properties->isDefiningFunction) {
                self::$insideArrayBeforeStart = $properties->isInsideArray;
                self::$isDefiningFunctionBeforeStart = true;
                $properties->isDefiningFunction = false;
            }

            if ($properties->isDefiningMethod) {
                self::$insideArrayBeforeStart = $properties->isInsideArray;
                self::$isDefiningMethodBeforeStart = true;
                $properties->isDefiningMethod = false;
            }

            $properties->fnLevel++;
            $properties->isInsideArray++;

            return false;
        }

        $properties->fnLevel--;
        $properties->isInsideArray--;

        if (self::$isDefiningFunctionBeforeStart && $properties->isInsideArray == self::$insideArrayBeforeStart) {
            self::$isDefiningFunctionBeforeStart = false;
            $properties->isDefiningFunction = true;
        }

        if (self::$isDefiningMethodBeforeStart && $properties->isInsideArray == self::$insideArrayBeforeStart) {
            self::$isDefiningMethodBeforeStart = false;
            $properties->isDefiningMethod = true;
        }

        $properties->isAttribute = $properties->force_close = $properties->collect = false;
        isset($properties->classes[$properties->c]) && $properties->c++;
        ClassReferenceFinder::forward();

        return true;
    }
}
