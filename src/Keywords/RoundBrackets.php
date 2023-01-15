<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class RoundBrackets
{
    public static function is($token)
    {
        return $token === '(' || $token === ')';
    }

    public static function body(ClassRefProperties $properties, &$tokens, &$t)
    {
        if ($t === '(' && ($properties->isDefiningFunction || $properties->isCatchException)) {
            $properties->isSignature = true;
            $properties->collect = true;
        } else {
            // so is calling a method by: ()
            $properties->collect = false;
        }

        // exclude namespaced function call: \Some\Func();
        if ($t === '(') {
            if (isset($properties->classes[$properties->c]) && ! $properties->isNewing) {
                // in php 7.4 or less
                unset($properties->classes[$properties->c]);
            } elseif (in_array(ClassReferenceFinder::$lastToken[0], [T_NAME_FULLY_QUALIFIED, T_NAME_QUALIFIED]) && ClassReferenceFinder::$secLastToken[0] !== T_NEW) {
                // in php 8.0 or more
                if ($properties->classes[$properties->c - 1][0] == ClassReferenceFinder::$lastToken) {
                    unset($properties->classes[--$properties->c]);
                }
            }
        }

        if ($t === ')') {
            $properties->isCatchException = $properties->isDefiningFunction = false;
        }
        isset($properties->classes[$properties->c]) && $properties->c++;
        ClassReferenceFinder::forward();

        return true;
    }
}