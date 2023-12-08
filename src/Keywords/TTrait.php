<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TTrait
{
    public static function is($token)
    {
        return $token === T_TRAIT;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->isInSideClass = true;
    }
}
