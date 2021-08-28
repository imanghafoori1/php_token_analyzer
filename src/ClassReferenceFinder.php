<?php

namespace Imanghafoori\TokenAnalyzer;

class ClassReferenceFinder
{
    public static $lastToken = [null, null, null];

    public static $secLastToken = [null, null, null];

    public static $token = [null, null, null];

    public static $keywords = [
        Keywords\T_Use::class,
        Keywords\ClassOrTrait::class,
        Keywords\T_Catch::class,
        Keywords\T_Namespace::class,
        Keywords\Property::class,
        Keywords\T_Function::class,
        Keywords\Variable::class,
        Keywords\T_Implements::class,
        Keywords\T_Extends::class,
        Keywords\WhiteSpaceOrCommand::class,
        Keywords\Boolean::class,
        Keywords\Comma::class,
        Keywords\SquareBracket::class,
        Keywords\Bracket::class,
        Keywords\CircleBracket::class,
        Keywords\Question::class,
        Keywords\DoubleColon::class,
        Keywords\Separator::class,
        Keywords\NameQualified::class,
        Keywords\T_New::class,
        Keywords\Pipe::class,
        Keywords\Colon::class,
    ];

    /**
     * @param  array  $tokens
     *
     * @return array
     */
    public static function process(&$tokens)
    {
        ! defined('T_NAME_QUALIFIED') && define('T_NAME_QUALIFIED', 3030);
        ! defined('T_NAME_FULLY_QUALIFIED') && define('T_NAME_FULLY_QUALIFIED', 3031);

        $namespace = '';
        $classes = [];
        $c = 0;
        $force_close = $implements = $collect = false;
        $trait = $isDefiningFunction = $isCatchException = $isSignature = $isDefiningMethod = $isInsideMethod = $isInSideClass = false;

        while (self::$token = current($tokens)) {
            next($tokens);
            $t = self::$token[0];
            $isContinue = false;

            foreach (self::$keywords as $keyword) {
                if ($keyword::is($t)) {
                    if ($keyword::body($tokens, $t, $isInSideClass, $force_close, $collect, $trait, $isCatchException, $namespace,
                        $isInsideMethod, $isDefiningFunction, $isDefiningMethod, $c, $implements,
                        $classes, $isSignature)) {
                        $isContinue = true;
                        break;
                    }
                }
            }
            if ($isContinue) {
                continue;
            }

            if ($collect && ! self::isBuiltinType(self::$token)) {
                $classes[$c][] = self::$token;
            }
            self::forward();
        }

        return [$classes, $namespace];
    }

    public static function forward()
    {
        self::$secLastToken = self::$lastToken;
        self::$lastToken = self::$token;
    }

    public static function isBuiltinType($token)
    {
        return \in_array($token[1], [
            'object',
            'string',
            'noreturn',
            'int',
            'private',
            'public',
            'protected',
            'float',
            'void',
            'false',
            'true',
            'null',
            'bool',
            'array',
            'mixed',
            'callable',
            '::',
            'iterable',
        ], true);
    }
}
