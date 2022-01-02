<?php

namespace Imanghafoori\TokenAnalyzer;

class ParseUseStatement
{
    public static function getExpandedRef($tokens, $className)
    {
        $refs = ParseUseStatement::parseUseStatements($tokens, $className);
        $rest = '';
        if ($className[0] !== '\\') {
            $parts = explode('\\', $className);
            $className = $parts[0] ?: $parts[1];
            array_shift($parts);
            $rest = implode('\\', $parts);
            $rest && $rest = '\\'.$rest;
        }

        return ($refs[1][$className][0] ?? $className).$rest;
    }

    public static function getUseStatementsByPath($namespacedClassName, $absPath)
    {
        return self::parseUseStatements(token_get_all(file_get_contents($absPath)), $namespacedClassName)[1];
    }

    public static function findClassReferences(&$tokens)
    {
        $imports = self::parseUseStatements($tokens);
        $imports = $imports[0] ?: [$imports[1]];
        [$classes, $namespace] = ClassReferenceFinder::process($tokens);

        return ClassRefExpander::expendReferences($classes, $imports, $namespace);
    }

    /**
     * Parses PHP code.
     *
     * @param $tokens
     * @param  null  $forClass
     *
     * @return array of [class => [alias => class, ...]]
     */
    public static function parseUseStatements($tokens, $forClass = null)
    {
        ! defined('T_NAME_QUALIFIED') && define('T_NAME_QUALIFIED', 3030);

        $namespace = $class = $classLevel = $level = null;
        $output = $uses = [];
        while ($token = \current($tokens)) {
            \next($tokens);
            switch (\is_array($token) ? $token[0] : $token) {
                case T_NAMESPACE:
                    $namespace = ltrim(self::FetchNS($tokens).'\\', '\\');
                    $uses = [];
                    break;

                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    if ($name = self::fetch($tokens, T_STRING)) {
                        $class = $namespace.$name;
                        $classLevel = $level + 1;
                        $output[$class] = $uses;
                        if ($class === $forClass) {
                            return [$output, $uses];
                        }
                    }
                    break;

                case T_USE:
                    while (! $class && ($name = self::FetchNS($tokens))) {
                        $name = ltrim($name, '\\');
                        if (self::fetch($tokens, '{')) {
                            while ($suffix = self::FetchNS($tokens)) {
                                if (self::fetch($tokens, T_AS)) {
                                    $uses[self::fetch($tokens, T_STRING)] = [$name.$suffix, $token[2]];
                                } else {
                                    $tmp = \explode('\\', $suffix);
                                    $uses[end($tmp)] = [$name.$suffix, $token[2]];
                                }
                                if (! self::fetch($tokens, ',')) {
                                    break;
                                }
                            }
                        } elseif (self::fetch($tokens, T_AS)) {
                            $uses[self::fetch($tokens, T_STRING)] = [$name, $token[2]];
                        } else {
                            $tmp = \explode('\\', $name);
                            $uses[\end($tmp)] = [$name, $token[2]];
                        }
                        if (! self::fetch($tokens, ',')) {
                            break;
                        }
                    }
                    break;

                case T_CURLY_OPEN:
                case T_DOLLAR_OPEN_CURLY_BRACES:
                case '{':
                    $level++;
                    break;

                case '}':
                    if ($level === $classLevel) {
                        $class = $classLevel = null;
                    }
                    $level--;
            }
        }

        return [$output, $uses];
    }

    public static function fetch(&$tokens, $take)
    {
        $result = null;

        $neutral = [T_DOC_COMMENT, T_WHITESPACE, T_COMMENT];

        while ($token = \current($tokens)) {
            [$token, $s,] = \is_array($token) ? $token : [$token, $token];

            if (\in_array($token, (array) $take, true)) {
                $result .= $s;
            } elseif (! \in_array($token, $neutral, true)) {
                break;
            }
            \next($tokens);
        }

        return $result;
    }

    private static function FetchNS(&$tokens)
    {
        return self::fetch($tokens, [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED]);
    }
}
