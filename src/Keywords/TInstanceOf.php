<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TInstanceOf
{
    public static function is($token)
    {
        return $token === T_INSTANCEOF;
    }

    public static function body(ClassRefProperties $properties)
    {
        return TNew::body($properties);
    }
}