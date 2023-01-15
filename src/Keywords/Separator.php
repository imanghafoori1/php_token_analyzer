<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Separator
{
    public static function is($token)
    {
        return $token === T_NS_SEPARATOR;
    }

    public static function body(ClassRefProperties $properties)
    {
        if (! $properties->force_close) {
            $properties->collect = true;
        }

        // Add the previous token,
        // In case the namespace does not start with '\'
        // like: App\User::where(...
        if (ClassReferenceFinder::$lastToken[0] === T_STRING && $properties->collect && ! isset($properties->classes[$properties->c])) {
            $properties->classes[$properties->c][] = ClassReferenceFinder::$lastToken;
        }
    }
}