<?php

namespace Imanghafoori\TokenAnalyzer;

class Condition
{
    public static $comparison = [
        '!=' => '==',
        '!==' => '===',
        '<=' => '>',
        '>=' => '<',
        '<' => '>=',
        '>' => '<=',
        '==' => '!=',
        '===' => '!==',
    ];

    public static $operators = [
        '==',
        '===',
        '>',
        '<',
        '>=',
        '<=',
        '!=',
        '!==',
    ];

    public static function negate($conditionTokens)
    {
        $found = false;

        $logic = ['&&', '||', 'or', 'and', '?:', '??', '-', '+', '*', '**', '%', '<=>'];

        $ops = array_merge(self::$operators, $logic);

        if (self::count($conditionTokens, self::$comparison) == 1 &&
            self::count($conditionTokens, $logic) == 0) {

            return self::replace($conditionTokens, self::$comparison);
        }

        foreach ($conditionTokens as $t) {
            if (\in_array($t[1] ?? $t[0], $ops)) {
                $found = true;
                break;
            }
        }

        if (! $found && $conditionTokens[0] != '!') {
            array_unshift($conditionTokens, '!');
        } elseif (! $found && $conditionTokens[0] == '!') {
            array_shift($conditionTokens);
        } else {
            array_unshift($conditionTokens, '(');
            array_unshift($conditionTokens, '!');
            $conditionTokens[] = ')';
        }

        return $conditionTokens;
    }

    private static function count($conditionTokens, $operators)
    {
        $level = $found = 0;
        foreach ($conditionTokens as $token) {
            $token === '(' && $level++;
            $token === ')' && $level--;
            if ($level === 0 && in_array($token[1] ?? $token[0], $operators)) {
                $found++;
            }
        }

        return $found;
    }

    private static function replace($conditionTokens, $operators)
    {
        $newTokens = [];
        foreach ($conditionTokens as $token) {
            $char = \is_array($token) ? $token[1] : $token[0];
            if (isset($operators[$char])) {
                $r = (array) $token;
                $r[1] = $operators[$char];

                $newTokens[] = $r;
            } else {
                $newTokens[] = $token;
            }
        }

        return $newTokens;
    }
}
