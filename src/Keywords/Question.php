<?php

namespace Imanghafoori\TokenAnalyzer\Keywords;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Question
{
    public static function is($token, $namespace = null)
    {
        return $token === '?';
    }

    public static function body()
    {
        // for a syntax like this:
        // public function __construct(?Payment $payment) { ... }
        // we skip collecting
        ClassReferenceFinder::forward();

        return true;
    }
}