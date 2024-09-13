<?php

namespace Imanghafoori\TokenAnalyzer;


class ClassReferenceFinder
{
    public static $lastToken = [null, null, null];

    public static $secLastToken = [null, null, null];

    public static $token = [null, null, null];

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
        ! defined('T_FN') && define('T_FN', -29);
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
        $tokenType = self::$token[0];

        $hashMap = [
            T_WHITESPACE => Keywords\Whitespace::class,
            '&' => Keywords\Whitespace::class,
            T_DOC_COMMENT => Keywords\Whitespace::class,
            T_COMMENT => Keywords\Whitespace::class,
            T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG => Keywords\Whitespace::class,
            '(' => Keywords\RoundBrackets::class,
            ')' => Keywords\RoundBrackets::class,
            T_VARIABLE => Keywords\Variable::class,
            T_ELLIPSIS => Keywords\Variable::class,
            ';' => Keywords\Semicolon::class,
            ',' => Keywords\Comma::class,
            '{' => Keywords\CurlyBrackets::class,
            '}' => Keywords\CurlyBrackets::class,
            T_USE => Keywords\TUse::class,
            T_DOUBLE_COLON => Keywords\DoubleColon::class,
            T_PUBLIC => Keywords\AccessModifiers::class,
            T_PROTECTED => Keywords\AccessModifiers::class,
            T_PRIVATE => Keywords\AccessModifiers::class,
            ']' => Keywords\SquareBrackets::class,
            '[' => Keywords\SquareBrackets::class,
            T_NAME_QUALIFIED => Keywords\NameQualified::class,
            T_NAME_FULLY_QUALIFIED => Keywords\NameQualified::class,
            T_DOUBLE_ARROW => Keywords\DoubleArrow::class,
            T_EXTENDS => Keywords\TExtends::class,
            T_NAMESPACE => Keywords\TNamespace::class,
            T_CLASS => Keywords\TClass::class,
            T_TRAIT => Keywords\TTrait::class,
            T_CATCH => Keywords\TCatch::class,
            ':' => Keywords\Colon::class,
            T_FN => Keywords\TFN::class,
            T_FUNCTION => Keywords\TFunction::class,
            T_IMPLEMENTS => Keywords\TImplements::class,
            T_BOOLEAN_AND => Keywords\Boolean::class,
            T_BOOLEAN_OR => Keywords\Boolean::class,
            T_LOGICAL_OR => Keywords\Boolean::class,
            T_LOGICAL_AND => Keywords\Boolean::class,
            T_IS_IDENTICAL => Keywords\Comparison::class,
            T_IS_EQUAL => Keywords\Comparison::class,
            T_ENUM => Keywords\Enum::class,
            '?' => Keywords\Question::class,
            T_NS_SEPARATOR => Keywords\Separator::class,
            T_NEW => Keywords\TNew::class,
            T_INSTANCEOF => Keywords\TInstanceOf::class,
            '|' => Keywords\Pipe::class,
            T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG => Keywords\Pipe::class,
            T_CONST => Keywords\TConst::class,
            T_CASE => Keywords\TCase::class,
            T_ATTRIBUTE => Keywords\TAttribute::class,
            T_INSTEADOF => Keywords\TInsteadOf::class,
        ];

        $keyword = ($hashMap[$tokenType] ?? '');
        if ($keyword && $keyword::body($properties, $tokens, $tokenType)) {
            return true;
        }

        return false;
    }

    private static function collectClassReferences(array &$tokens)
    {
        $cursor = new ClassRefProperties;

        while ($token = self::$token = current($tokens)) {
            next($tokens);
            $co = self::shouldCollect($cursor, $tokens);

            if ($co) {
                continue;
            }

            if ($cursor->collect && (! self::isBuiltinType($token) || $cursor->isNewing)) {
                $cursor->addRef($token);
            }
            self::forward();
        }

        return $cursor;
    }
}
