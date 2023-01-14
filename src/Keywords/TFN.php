<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TFN
{
    public static function is($token)
    {
        return defined('T_FN') && $token === T_FN;
    }

    public static function body(ClassRefProperties $properties)
    {
        $properties->fnLevel = 0;
        $properties->isDefiningFunction = true;
    }
}