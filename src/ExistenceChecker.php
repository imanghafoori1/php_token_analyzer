<?php

namespace Imanghafoori\TokenAnalyzer;

use Throwable;

class ExistenceChecker
{
    public static function check($entityRef, $absFilePath): bool
    {
        if (self::entityExists($entityRef)) {
            return true;
        }

        try {
            require_once $absFilePath;
        } catch (Throwable $e) {
            return false;
        }

        if (self::entityExists($entityRef)) {
            return true;
        }

        return false;
    }

    private static function entityExists($entityRef)
    {
        return class_exists($entityRef) ||
            interface_exists($entityRef) ||
            trait_exists($entityRef) ||
            function_exists($entityRef) ||
            (function_exists('enum_exists') && enum_exists($entityRef));
    }
}
