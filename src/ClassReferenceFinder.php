<?php

namespace Imanghafoori\TokenAnalyzer;

use Closure;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use RuntimeException;

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

        return [$cursor->classes, $cursor->namespace];
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
            if ($token[0] !== T_DOC_COMMENT) {
                continue;
            }
            try {
                $doc = $docblock->create(
                    str_replace('?', '', $token[1]),
                    new Context('q1w23e4rt___ffff000'),
                    new Location($token[2], 4)
                );
            } catch (RuntimeException $e) {
                continue;
            }

            $refs = array_merge($refs, self::getRefsInDocblock($doc));
        }

        return $refs;
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

    private static function getRefsInDocblock(DocBlock $docblock): array
    {
        $line = $docblock->getLocation()->getLineNumber();

        $readRef = self::getRefReader($docblock, $line);

        return array_merge(
            self::readMethodTag($docblock, $line),
            self::getMixins($docblock, $line),
            $readRef('param'),
            $readRef('var'),
            $readRef('return'),
            $readRef('throws'),
            $readRef('property'),
            $readRef('property-read'),
            $readRef('property-write'),
            $readRef('see')
        );
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

    private static function addRef($refsInDocBlock, int $line, array $allRefs)
    {
        foreach ($refsInDocBlock as $ref) {
            $ref = str_replace('[]', '', $ref);
            $ref = trim($ref, '<>');
            $ref && self::shouldBeCollected($ref) && $allRefs[] = [
                // Remove "?" from nullable references like: "?User"
                'class' => str_replace('\\q1w23e4rt___ffff000\\', '', $ref),
                'line' => $line,
            ];
        }

        return $allRefs;
    }

    private static function defineConstants()
    {
        ! defined('T_NAME_QUALIFIED') && define('T_NAME_QUALIFIED', -352);
        ! defined('T_NAME_FULLY_QUALIFIED') && define('T_NAME_FULLY_QUALIFIED', -373);
        ! defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG', -385);
        ! defined('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG', -386);
        ! defined('T_READONLY') && define('T_READONLY', -387);
        ! defined('T_ENUM') && define('T_ENUM', -721);
    }

    public static function explode($ref): array
    {
        $ref = str_replace(',', '|', (string) $ref);

        return explode('|', $ref);
    }

    private static function readMethodTag(DocBlock $docblock, int $line)
    {
        $refs = [];

        foreach ($docblock->getTagsByName('method') as $method) {
            $refs = self::addRef(self::explode($method->getReturnType()), $line, $refs);

            foreach ($method->getArguments() as $argument) {
                $_refs = self::explode(str_replace('?', '', (string) $argument['type']));
                $refs = self::addRef($_refs, $line, $refs);
            }
        }

        return $refs;
    }

    private static function getRefReader(DocBlock $docblock, int $line): Closure
    {
        return function ($tagName) use ($docblock, $line) {
            $refs = [];
            foreach ($docblock->getTagsByName($tagName) as $ref) {
                if (method_exists($ref, 'getType') && $ref->getType() && method_exists($ref->getType(), 'getFqsen')) {
                    $refs = self::addRef(self::explode($ref->getType()->getFqsen()), $line, $refs);

                    // For support like this: "Collection<Product|User|Test>"
                    if (method_exists($ref->getType(), 'getValueType') && ($types = $ref->getType()->getValueType())) {
                        if ($types instanceof Compound) {
                            foreach ($types as $type) {
                                if (!method_exists($type, 'getFqsen')) {
                                    continue;
                                }
                                $refs = self::addRef(self::explode($type->getFqsen()), $line, $refs);
                            }
                        } else if (method_exists($types, 'getFqsen')) {
                            $refs = self::addRef(self::explode($types->getFqsen()), $line, $refs);
                        }
                    }
                    continue;
                }
                if (! method_exists($ref, 'getType')) {
                    $ref = (string) $ref;
                    $ref && $refs = self::addRef(self::explode($ref), $line, $refs);
                    continue;
                }
                // this finds the "Money" ref in: " @var array<int, class-string<Money>> "
                $type = $ref->getType();
                if (! $type) {
                    continue;
                }
                if (! method_exists($type, 'getValueType')) {
                    $refs = self::addRef(self::explode($ref->getType()), $line, $refs);
                    continue;
                }
                $value = $ref->getType()->getValueType();
                if (! $value) {
                    continue;
                }
                $v = method_exists($value, 'getFqsen') ? $value->getFqsen() : $value->__toString();

                $refs = self::addRef(self::explode($v), $line, $refs);
            }

            return $refs;
        };
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
                $cursor->classes[$cursor->c][] = $token;
            }
            self::forward();
        }

        return $cursor;
    }

    private static function shouldBeCollected(string $ref)
    {
        return ! self::isBuiltinType([0, $ref]) && ! Str::contains($ref, ['<', '>', '$', ':', '(', ')', '{', '}', '-']);
    }
}
