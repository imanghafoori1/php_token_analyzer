<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class CurlyBrackets
{
    public static function is($token)
    {
        return $token === '{' || $token === '}';
    }

    public static function body(ClassRefProperties $properties, &$tokens, &$t)
    {
        if ($t === '{') {
            return self::open($properties);
        }

        return self::close($properties, $tokens, $t);
    }

    protected static function open(ClassRefProperties $properties)
    {
        if ($properties->isDefiningMethod) {
            $properties->isInsideMethod = true;
        }
        $properties->isDefiningMethod = $properties->implements = $properties->isSignature = false;
        // After "extends \Some\other\Class_v"
        // we need to switch to the next level.
        if ($properties->collect) {
            isset($properties->classes[$properties->c]) && $properties->c++;
            $properties->collect = false;
        }
        ClassReferenceFinder::forward();

        return true;
    }

    protected static function close(ClassRefProperties $properties, &$tokens, &$t)
    {
        $properties->trait = $properties->force_close = false;

        // Interface methods end up with ";"
        $t === ';' && ($properties->declaringProperty = $properties->isImporting = $properties->isSignature = false);
        $properties->collect && isset($properties->classes[$properties->c]) && $properties->c++;
        $properties->collect = false;

        ClassReferenceFinder::forward();

        return true;
    }
}