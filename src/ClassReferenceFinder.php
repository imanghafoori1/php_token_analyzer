<?php

namespace Imanghafoori\TokenAnalyzer;


class ClassReferenceFinder
{
    public static $lastToken = [null, null, null];

    public static $secLastToken = [null, null, null];

    public static $token = [null, null, null];

    public static $keywords = [
        Keywords\TUse::class,
        Keywords\DoubleArrow::class,
        Keywords\TClass::class,
        Keywords\TTrait::class,
        Keywords\TCatch::class,
        Keywords\TNamespace::class,
        Keywords\AccessModifiers::class,
        Keywords\TFN::class,
        Keywords\TFunction::class,
        Keywords\Variable::class,
        Keywords\TImplements::class,
        Keywords\TInsteadOf::class,
        Keywords\TExtends::class,
        Keywords\Whitespace::class,
        Keywords\Semicolon::class,
        Keywords\Boolean::class,
        Keywords\Comma::class,
        Keywords\Enum::class,
        Keywords\SquareBrackets::class,
        Keywords\CurlyBrackets::class,
        Keywords\RoundBrackets::class,
        Keywords\Question::class,
        Keywords\DoubleColon::class,
        Keywords\Separator::class,
        Keywords\NameQualified::class,
        Keywords\TNew::class,
        Keywords\TInstanceOf::class,
        Keywords\Pipe::class,
        Keywords\TConst::class,
        Keywords\TCase::class,
        Keywords\Colon::class,
        Keywords\Comparison::class,
        Keywords\TAttribute::class,
    ];

    public static $ignoreRefs = [
        'array',
        'bool',
        'callable',
        'false',
        'float',
        'int',
        'iterable',
        'mixed',
        'never',
        'null',
        'object',
        'private',
        'public',
        'protected',
        'parent',
        'static',
        'self',
        'string',
        'true',
        'void',
        '::',
        'list',
        'scalar',
        'resource',
    ];

    /**
     * @param  array  $tokens
     *
     * @return array
     */
    public static function process(&$tokens)
    {
        self::defineConstants();

        $cursor = self::collectClassReferences($tokens);

        self::joinClassRefSegments($cursor);

        return [$cursor->classes, $cursor->namespace, $cursor->attributeRefs];
    }

    public static function forward()
    {
        self::$secLastToken = self::$lastToken;
        self::$lastToken = self::$token;
    }

    public static function isBuiltinType($token)
    {
        return \in_array(strtolower($token[1]), self::$ignoreRefs, true)
            || \in_array($token[0], [T_READONLY]);
    }

    public static function getExpandedDocblockRefs($imports, $docblockRefs, $namespace)
    {
        $importedRefs = [];
        foreach ($imports as $_imps) {
            $importedRefs = array_merge($importedRefs, $_imps);
        }

        foreach ($docblockRefs as $i => $ref) {
            $class = $ref['class'];
            if ($class === '' || $class[0] === '\\') {
                continue;
            }
            if (isset($importedRefs[$class])) {
                $docblockRefs[$i]['class'] = $importedRefs[$class][0];
            } else {
                $docblockRefs[$i]['class'] = $namespace.'\\'.$ref['class'];
            }
        }

        return $docblockRefs;
    }

    public static function defineConstants()
    {
        ! defined('T_NAME_QUALIFIED') && define('T_NAME_QUALIFIED', -352);
        ! defined('T_NAME_FULLY_QUALIFIED') && define('T_NAME_FULLY_QUALIFIED', -373);
        ! defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG', -385);
        ! defined('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG', -386);
        ! defined('T_READONLY') && define('T_READONLY', -387);
        ! defined('T_ENUM') && define('T_ENUM', -121);
        ! defined('T_ATTRIBUTE') && define('T_ATTRIBUTE', -226);
    }

    private static function joinClassRefSegments(ClassRefProperties $properties)
    {
        foreach ($properties->classes as $i => $classTokens) {
            $result = [T_STRING, '', $classTokens[0][2]];

            foreach ($classTokens as $token) {
                $result[1] .= $token[1];
            }
            $properties->classes[$i] = [$result];
        }
    }

    private static function shouldCollect(ClassRefProperties $properties, array &$tokens)
    {
        $t = self::$token[0];

        foreach (self::$keywords as $keyword) {
            if ($keyword::is($t, $properties->namespace)) {
                if ($keyword::body($properties, $tokens, $t)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function collectClassReferences(array &$tokens)
    {
        $cursor = new ClassRefProperties;

        while ($token = self::$token = current($tokens)) {
            next($tokens);
            if (self::shouldCollect($cursor, $tokens)) {
                continue;
            }

            if ($cursor->collect && ! self::isBuiltinType($token)) {
                $cursor->addRef($token);
            }
            self::forward();
        }

        return $cursor;
    }
}
