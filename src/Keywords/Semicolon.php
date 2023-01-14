<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Semicolon
{
    public static function is($token)
    {
        return $token === ';';
    }

    public static function body(ClassRefProperties $properties, &$tokens, &$t)
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