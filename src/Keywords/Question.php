<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;
use Imanghafoori\TokenAnalyzer\ClassRefProperties;

class Question
{
    public static function is($token)
    {
        return $token === '?';
    }

    public static function body(ClassRefProperties $properties)
    {
        // for a syntax like this:
        // public function __construct(?Payment $payment) { ... }
        // we skip collecting
        if (! $properties->isSignature && ! $properties->declaringProperty) {
            $properties->collect = false;
        }
        ClassReferenceFinder::forward();

        return true;
    }
}