<?php

namespace Imanghafoori\TokenAnalyzer;

use Imanghafoori\TokenAnalyzer\Keywords\AccessModifiers;
use Imanghafoori\TokenAnalyzer\Keywords\Boolean;
use Imanghafoori\TokenAnalyzer\Keywords\CircleBrackets;
use Imanghafoori\TokenAnalyzer\Keywords\CloseCurlyBrackets;
use Imanghafoori\TokenAnalyzer\Keywords\CloseSquareBracket;
use Imanghafoori\TokenAnalyzer\Keywords\Colon;
use Imanghafoori\TokenAnalyzer\Keywords\Comma;
use Imanghafoori\TokenAnalyzer\Keywords\DoubleArrow;
use Imanghafoori\TokenAnalyzer\Keywords\DoubleColon;
use Imanghafoori\TokenAnalyzer\Keywords\NameQualified;
use Imanghafoori\TokenAnalyzer\Keywords\CurlyBrackets;
use Imanghafoori\TokenAnalyzer\Keywords\RoundBrackets;
use Imanghafoori\TokenAnalyzer\Keywords\SquareBrackets;
use Imanghafoori\TokenAnalyzer\Keywords\Pipe;
use Imanghafoori\TokenAnalyzer\Keywords\Question;
use Imanghafoori\TokenAnalyzer\Keywords\Semicolon;
use Imanghafoori\TokenAnalyzer\Keywords\Separator;
use Imanghafoori\TokenAnalyzer\Keywords\TCase;
use Imanghafoori\TokenAnalyzer\Keywords\TCatch;
use Imanghafoori\TokenAnalyzer\Keywords\TClass;
use Imanghafoori\TokenAnalyzer\Keywords\TConst;
use Imanghafoori\TokenAnalyzer\Keywords\TExtends;
use Imanghafoori\TokenAnalyzer\Keywords\TFN;
use Imanghafoori\TokenAnalyzer\Keywords\TFunction;
use Imanghafoori\TokenAnalyzer\Keywords\TImplements;
use Imanghafoori\TokenAnalyzer\Keywords\TInstanceOf;
use Imanghafoori\TokenAnalyzer\Keywords\TInsteadOf;
use Imanghafoori\TokenAnalyzer\Keywords\TNamespace;
use Imanghafoori\TokenAnalyzer\Keywords\TNew;
use Imanghafoori\TokenAnalyzer\Keywords\TTrait;
use Imanghafoori\TokenAnalyzer\Keywords\TUse;
use Imanghafoori\TokenAnalyzer\Keywords\Variable;
use Imanghafoori\TokenAnalyzer\Keywords\Whitespace;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Types\Context;

class ClassReferenceFinder
{
    public static $lastToken = [null, null, null];

    public static $secLastToken = [null, null, null];

    public static $token = [null, null, null];

    public static $keywords = [
        TUse::class,
        DoubleArrow::class,
        TClass::class,
        TTrait::class,
        TCatch::class,
        TNamespace::class,
        AccessModifiers::class,
        TFN::class,
        TFunction::class,
        Variable::class,
        TImplements::class,
        TInsteadOf::class,
        TExtends::class,
        Whitespace::class,
        Semicolon::class,
        Boolean::class,
        Comma::class,
        SquareBrackets::class,
        CurlyBrackets::class,
        RoundBrackets::class,
        Question::class,
        DoubleColon::class,
        Separator::class,
        NameQualified::class,
        TNew::class,
        TInstanceOf::class,
        Pipe::class,
        TConst::class,
        TCase::class,
        Colon::class,
    ];

    /**
     * @param  array  $tokens
     *
     * @return array
     */
    public static function process(&$tokens)
    {
        self::defineConstants();
        $properties = new ClassRefProperties;

        while (self::$token = current($tokens)) {
            next($tokens);
            $t = self::$token[0];
            $isContinue = false;

            foreach (self::$keywords as $keyword) {
                if ($keyword::is($t, $properties->namespace)) {
                    if ($keyword::body($properties, $tokens, $t)) {
                        $isContinue = true;
                        break;
                    }
                }
            }

            if ($isContinue) {
                continue;
            }

            if ($properties->collect && ! self::isBuiltinType(self::$token)) {
                $properties->classes[$properties->c][] = self::$token;
            }
            self::forward();
        }

        foreach ($properties->classes as $i => $classTokens) {
            $result = [
                T_STRING,
                '',
                $classTokens[0][2]

            ];
            foreach ($classTokens as $token) {
                $result[1] .= $token[1];
            }
            $properties->classes[$i] = [$result];
        }

        return [$properties->classes, $properties->namespace];
    }

    public static function forward()
    {
        self::$secLastToken = self::$lastToken;
        self::$lastToken = self::$token;
    }

    public static function isBuiltinType($token)
    {
        return \in_array(strtolower($token[1]), [
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
        ], true) || \in_array($token[0], [T_READONLY]);
    }

    public static function readRefsInDocblocks($tokens)
    {
        self::defineConstants();
        $docblock = DocBlockFactory::createInstance();

        $refs = [];
        foreach ($tokens as $token) {
            if ($token[0] === T_DOC_COMMENT) {
                $refs = array_merge($refs, self::getRefsInDocblock(
                    $docblock->create(
                        $token[1],
                        new Context('q1w23e4rt___ffff000'),
                        new Location($token[2], 4)
                    )
                ));
            }
        }

        return $refs;
    }

    private static function getRefsInDocblock(DocBlock $docblock): array
    {
        $refs = [];
        $line = $docblock->getLocation()->getLineNumber();
        foreach ($docblock->getTagsByName('method') as $method) {
            $refs = self::addRef(explode('|', (string) $method->getReturnType()), $line, $refs);

            foreach ($method->getArguments() as $argument) {
                $_refs = explode('|', str_replace('?', '', (string) $argument['type']));
                $refs = self::addRef($_refs, $line, $refs);
            }
        }

        $readRef = function ($tagName) use ($docblock, $line) {
            $refs = [];
            foreach ($docblock->getTagsByName($tagName) as $ref) {
                if (method_exists($ref, 'getType') && $ref->getType() && method_exists($ref->getType(), 'getFqsen')) {
                    $refs = self::addRef((explode('|', $ref->getType()->getFqsen())), $line, $refs);
                    continue;
                }
                if (! method_exists($ref, 'getType')) {
                    $ref = (string) $ref;
                    $ref && $refs = self::addRef(explode('|', $ref), $line, $refs);
                    continue;
                }
                // this finds the "Money" ref in: " @var array<int, class-string<Money>> "
                $type = $ref->getType();
                if (! $type) {
                    continue;
                }
                if (! method_exists($type, 'getValueType')) {
                    $refs = self::addRef(explode('|', (string) $ref->getType()), $line, $refs);
                    continue;
                }
                $value = $ref->getType()->getValueType();
                if (! $value) {
                    continue;
                }
                $v = method_exists($value, 'getFqsen') ? $value->getFqsen() : $value->__toString();

                $refs = self::addRef(explode('|', $v), $line, $refs);
            }

            return $refs;
        };

        $refs = array_merge(
            $refs,
            self::getMixins($docblock, $line),
            $readRef('param'),
            $readRef('var'),
            $readRef('return'),
            $readRef('throws'),
            $readRef('see')
        );

        return $refs;
    }

    private static function getMixins(DocBlock $docblock, int $line)
    {
        $mixins = [];
        foreach ($docblock->getTagsByName('mixin') as $ref) {
            $desc = $ref->getDescription();
            if ($desc && $body = $desc->getBodyTemplate()) {
                $mixins[] = [
                    'line' => $line,
                    'class' => $body,
                ];
            }
        }

        return $mixins;
    }

    private static function addRef($_refs, int $line, array $refs): array
    {
        foreach ($_refs as $ref) {
            $ref = str_replace('[]', '', $ref);
            ! self::isBuiltinType([0, $ref]) && ! Str::startsWith($ref, ['array<int', 'array<string']) && $ref !== 'class-string' && $refs[] = [
                'class' => str_replace('\\q1w23e4rt___ffff000\\', '', $ref),
                'line' => $line,
            ];
        }

        return $refs;
    }

    private static function defineConstants()
    {
        ! defined('T_NAME_QUALIFIED') && define('T_NAME_QUALIFIED', -352);
        ! defined('T_NAME_FULLY_QUALIFIED') && define('T_NAME_FULLY_QUALIFIED', -373);
        ! defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG', -385);
        ! defined('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG', -386);
        ! defined('T_READONLY') && define('T_READONLY', -387);
    }
}
