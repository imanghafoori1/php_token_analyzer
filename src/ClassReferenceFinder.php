<?php

namespace Imanghafoori\TokenAnalyzer;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Types\Context;

class ClassReferenceFinder
{
    private static $lastToken = [null, null, null];

    private static $secLastToken = [null, null, null];

    private static $token = [null, null, null];

    /**
     * @param  array  $tokens
     *
     * @return array
     */
    public static function process(&$tokens)
    {
        ! defined('T_NAME_QUALIFIED') && define('T_NAME_QUALIFIED', -352);
        ! defined('T_NAME_FULLY_QUALIFIED') && define('T_NAME_FULLY_QUALIFIED', -373);
        ! defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG', -385);
        ! defined('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG') && define('T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG', -386);
        ! defined('T_READONLY') && define('T_READONLY', -387);

        $namespace = '';
        $classes = [];
        $c = 0;
        $declaringProperty = $force_close = $implements = $collect = false;
        $isImporting = $trait = $isDefiningFunction = $isCatchException = $isSignature = $isDefiningMethod = $isInsideMethod = $isInSideClass = false;

        $fnLevel = $isInsideArray = 0;
        while (self::$token = current($tokens)) {
            next($tokens);
            $t = self::$token[0];

            if ($t === T_USE) {
                ! $isInSideClass && $isImporting = true;
                next($tokens);
                // use function Name\Space\function_name;
                if (current($tokens)[0] === T_FUNCTION) {
                    // we do not collect the namespaced function name
                    next($tokens);
                    $force_close = true;
                    self::forward();
                    continue;
                }

                // function () use ($var) {...}
                // for this type of use we do not care and continue;
                // who cares?!
                if (self::$lastToken === ')') {
                    self::forward();
                    continue;
                }

                // Since we don't want to collect use statements (imports)
                // and we want to collect the used traits on the class.
                if (! $isInSideClass) {
                    $force_close = true;
                    $collect = false;
                } else {
                    $collect = $trait = true;
                }
                self::forward();
                continue;
            } elseif ($t === T_DOUBLE_ARROW) {
                if ($fnLevel === 0) {
                    // it means that we have reached: fn($r = ['a' => 'b']) => '-'
                    $isSignature = $isDefiningFunction = false;
                }
                if ($collect) {
                    $c++;
                    $collect = false;
                    self::forward();
                    continue;
                }
            } elseif ($t === T_CLASS || $t === T_TRAIT) {
                // new class {... }
                // ::class
                if (self::$lastToken[0] === T_NEW || self::$lastToken[0] === T_DOUBLE_COLON) {
                    $collect = false;
                    self::forward();
                    continue;
                }
                $isInSideClass = true;
            } elseif ($t === T_CATCH) {
                $collect = true;
                $isCatchException = true;
                continue;
            } elseif ($t === T_NAMESPACE && ! $namespace && self::$lastToken[0] !== T_DOUBLE_COLON) {
                $collect = false;
                next($tokens);
                while (current($tokens)[0] !== ';') {
                    (! in_array(current($tokens)[0], [T_COMMENT, T_WHITESPACE])) && $namespace .= current($tokens)[1];
                    next($tokens);
                }
                next($tokens);
                continue;
            } elseif (\in_array($t, [T_PUBLIC, T_PROTECTED, T_PRIVATE], true) && self::$lastToken[0] !== T_AS) {
                $_ = next($tokens);

                if ($_[0] === T_STATIC && $_[1] === 'static') {
                    while (($_ = next($tokens))[0] === T_WHITESPACE) {}
                }

                if ($_[0] === T_CONST || $_[0] === T_FUNCTION) {
                    continue;
                }

                $collect = true;
                self::forward();
                $declaringProperty = true;
                $isInsideMethod = false;
                continue;
            } elseif (defined('T_FN') && $t === T_FN) {
                $fnLevel = 0;
                $isDefiningFunction = true;
            } elseif ($t === T_FUNCTION) {
                $isDefiningFunction = true;
                if ($isInSideClass and ! $isInsideMethod) {
                    $isDefiningMethod = true;
                }
            } elseif ($t === T_VARIABLE || $t === T_ELLIPSIS) {
                $collect && isset($classes[$c]) && $c++;
                $collect = false;
                self::forward();
                // we do not want to collect variables
                continue;
            } elseif ($t === T_IMPLEMENTS) {
                $collect = $implements = true;
                isset($classes[$c]) && $c++;
                self::forward();
                continue;
            } elseif ($t === T_EXTENDS) {
                $collect = true;
                //isset($classes[$c]) && $c++;
                self::forward();
                continue;
            } elseif ($t === T_WHITESPACE || $t === '&' || $t === T_COMMENT || $t === T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG) {
                // We do not want to keep track of white spaces or collect them
                continue;
            } elseif (in_array($t, [';', '}', T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_OR, T_LOGICAL_AND], true)) {
                $trait = $force_close = false;

                // Interface methods end up with ";"
                $t === ';' && ($declaringProperty = $isImporting = $isSignature = false);
                $collect && isset($classes[$c]) && $c++;
                $collect = false;

                self::forward();
                continue;
            } elseif ($t === ',') {
                // to avoid mistaking commas in default array values with commas between args
                // example:   function hello($arg = [1, 2]) { ... }
                $collect = ($isSignature && $isInsideArray === 0) || $implements || $trait;
                $isInSideClass && ($force_close = false);
                // for method calls: foo(new Hello, $var);
                // we do not want to collect after comma.
                isset($classes[$c]) && $c++;
                self::forward();
                continue;
            } elseif ($t === '[') {
                $fnLevel++;
                $isInsideArray++;
            } elseif ($t === ']') {
                $fnLevel--;
                $isInsideArray--;
                $force_close = $collect = false;
                isset($classes[$c]) && $c++;
                self::forward();
                continue;
            } elseif ($t === '{') {
                if ($isDefiningMethod) {
                    $isInsideMethod = true;
                }
                $isDefiningMethod = $implements = $isSignature = false;
                // After "extends \Some\other\Class_v"
                // we need to switch to the next level.
                if ($collect) {
                    isset($classes[$c]) && $c++;
                    $collect = false;
                }
                self::forward();
                continue;
            } elseif ($t === '(' || $t === ')') {
                // wrong...
                if ($t === '(' && ($isDefiningFunction || $isCatchException)) {
                    $isSignature = true;
                    $collect = true;
                } else {
                    // so is calling a method by: ()
                    $collect = false;
                }
                if ($t === ')') {
                    $isCatchException = $isDefiningFunction = false;
                }
                isset($classes[$c]) && $c++;
                self::forward();
                continue;
            } elseif ($t === '?') {
                // for a syntax like this:
                // public function __construct(?Payment $payment) { ... }
                // we skip collecting
                if (! $isSignature && ! $declaringProperty) {
                    $collect = false;
                }
                self::forward();
                continue;
            } elseif ($t === T_DOUBLE_COLON) {
                // When we reach the ::class syntax.
                // we do not want to treat: $var::method(), self::method()
                // as a real class name, so it must be of type T_STRING
                if (! $collect && self::$lastToken[0] === T_STRING && ! \in_array(self::$lastToken[1], ['parent', 'self', 'static'], true) && (self::$secLastToken[1] ?? null) !== '->') {
                    $classes[$c][] = self::$lastToken;
                }
                $collect = false;
                isset($classes[$c]) && $c++;
                self::forward();
                continue;
            } elseif ($t === T_NS_SEPARATOR) {
                if (! $force_close) {
                    $collect = true;
                }

                // Add the previous token,
                // In case the namespace does not start with '\'
                // like: App\User::where(...
                if (self::$lastToken[0] === T_STRING && $collect && ! isset($classes[$c])) {
                    $classes[$c][] = self::$lastToken;
                }
            } elseif ($t === T_NAME_QUALIFIED || $t === T_NAME_FULLY_QUALIFIED) {
                if (! $isImporting) {
                    $classes[$c++][] = self::$token;
                    $collect = false;
                    self::forward();
                    continue;
                }
                //self::forward();
            } elseif ($t === T_NEW || $t === T_INSTANCEOF) {
                // We start to collect tokens after the new keyword.
                // unless we reach a variable name.
                // currently, tokenizer recognizes CONST NEW = 1; as new keyword.
                // case New = 'new';
                ! in_array(self::$lastToken[0], [T_CONST, T_CASE, T_DOUBLE_COLON]) && $collect = true;
                self::forward();

                // we do not want to collect the new keyword itself
                continue;
            } elseif ($t === '|' || $t === T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG) {
                isset($classes[$c]) && $c++;
                self::forward();

                continue;
            } elseif ($t === ':') {
                if ($isSignature) {
                    $collect = true;
                } else {
                    $collect = false;
                    isset($classes[$c]) && $c++;
                }
                self::forward();
                continue;
            }

            if ($collect && ! self::isBuiltinType(self::$token)) {
                $classes[$c][] = self::$token;
            }
            self::forward();
        }

        return [$classes, $namespace];
    }

    protected static function forward()
    {
        self::$secLastToken = self::$lastToken;
        self::$lastToken = self::$token;
    }

    public static function isBuiltinType($token)
    {
        return \in_array($token[1], [
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
            ! self::isBuiltinType([0, $ref]) && ! Str::startsWith($ref, ['array<int', 'array<string']) && $ref !== 'class-string' && $refs[] = [
                'class' => str_replace('\\q1w23e4rt___ffff000\\', '', $ref),
                'line' => $line,
            ];
        }

        return $refs;
    }
}
