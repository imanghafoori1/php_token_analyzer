<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Enum
{
    public static function is($token)
    {
        return $token === T_ENUM;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->isInSideClass = true;
    }
}
