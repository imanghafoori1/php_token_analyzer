<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class DoubleArrow
{
    public static function is($token)
    {
        return $token === T_DOUBLE_ARROW;
    }

    public static function body(ClassRefProperties $properties)
    {
        if ($properties->fnLevel === 0) {
            // it means that we have reached: fn($r = ['a' => 'b']) => '-'
            $properties->isSignature = $properties->isDefiningFunction = false;
        }
        if ($properties->collect) {
            $properties->c++;
            $properties->collect = false;
            ClassReferenceFinder::forward();

            return true;
        }
    }
}