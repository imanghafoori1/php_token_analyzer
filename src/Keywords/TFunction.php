<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TFunction
{
    public static function is($token)
    {
        return $token === T_FUNCTION;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->isDefiningFunction = true;
        if ($properties->isInSideClass && ! $properties->isInsideMethod) {
            $properties->isDefiningMethod = true;
        }
    }
}
