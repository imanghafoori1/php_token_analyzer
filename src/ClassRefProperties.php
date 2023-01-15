<?php

namespace Imanghafoori\TokenAnalyzer;

class ClassRefProperties
{
    public $namespace = '';

    public $classes = [];

    public $c = 0;

    public $fnLevel = 0;

    public $isInsideArray = 0;

    public $declaringProperty = false;

    public $isImporting = false;

    public $isInSideClass = false;

    public $force_close = false;

    public $collect = false;

    public $trait = false;

    public $isCatchException = false;

    public $isInsideMethod = false;

    public $isDefiningFunction = false;

    public $isDefiningMethod = false;

    public $implements = false;

    public $isSignature = false;

    public $isNewing = false;

}