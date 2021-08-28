<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Bracket
{
    public static function is($token, $namespace = null)
    {
        return $token === '{';
    }

    public static function body(&$tokens, &$t, &$isInSideClass, &$force_close, &$collect, &$trait, &$isCatchException, &$namespace,
        &$isInsideMethod, &$isDefiningFunction, &$isDefiningMethod, &$c, &$implements, &$classes, &$isSignature)
    {
        if ($isDefiningMethod) {
            $isInsideMethod = true;
        }
        $isDefiningMethod = $implements = $isSignature = false;
        // After "extends \Some\other\Class_v"
        // we need to switch to the next level.
        if ($collect) {
            isset($classes[$c]) && $c++;
            $collect = false;
        }
        ClassReferenceFinder::forward();

        return true;
    }
}