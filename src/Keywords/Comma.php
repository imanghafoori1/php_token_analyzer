<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Comma
{
    public static function is($token)
    {
        return $token === ',';
    }

    public static function body(ClassRefProperties $properties)
    {
        // to avoid mistaking commas in default array values with commas between args
        // example:   function hello($arg = [1, 2]) { ... }
        $properties->collect = ($properties->isSignature && $properties->isInsideArray === 0) || $properties->implements || $properties->trait;
        $properties->isInSideClass && ($properties->force_close = false);
        // for method calls: foo(new Hello, $var);
        // we do not want to collect after comma.
        isset($properties->classes[$properties->c]) && $properties->c++;
        ClassReferenceFinder::forward();

        return true;
    }
}