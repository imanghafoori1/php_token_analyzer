<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class TUse
{
    public static function is($token)
    {
        return $token === T_USE;
    }

    public static function body(ClassRefProperties $properties, &$tokens)
    {
        ! $properties->isInSideClass && $properties->isImporting = true;
        next($tokens);
        // use function Name\Space\function_name;
        if (current($tokens)[0] === T_FUNCTION) {
            // we do not collect the namespaced function name
            next($tokens);
            $properties->force_close = true;
            ClassReferenceFinder::forward();

            return true;
        }

        // function () use ($var) {...}
        // for this type of use we do not care and continue;
        // who cares?!
        if (ClassReferenceFinder::$lastToken === ')') {
            ClassReferenceFinder::forward();

            return true;
        }

        // Since we don't want to collect use statements (imports)
        // and we want to collect the used traits on the class.
        if (! $properties->isInSideClass) {
            $properties->force_close = true;
            $properties->collect = false;
        } else {
            $properties->collect = $properties->trait = true;
        }
        ClassReferenceFinder::forward();

        return true;
    }
}