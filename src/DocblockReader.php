<?php

namespace Imanghafoori\TokenAnalyzer;

use Closure;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Types\Context;
use RuntimeException;

class DocblockReader
{
    private static $ignoreTemplateRefs = [
        'array-key',
        'object',
    ];

    public static function readRefsInDocblocks($tokens)
    {
        ClassReferenceFinder::defineConstants();
        $docblock = DocBlockFactory::createInstance();

        $refs = [];
        $generics = [];
        foreach ($tokens as $token) {
            if ($token[0] !== T_DOC_COMMENT) {
                continue;
            }
            try {
                [, $content, $line] = $token;
                $doc = self::read($docblock, str_replace('?', '', $content), $line);
            } catch (RuntimeException $e) {
                try {
                    $doc = self::read($docblock, self::normalizeDocblockContent($content), $line);
                } catch (RuntimeException $e) {
                    continue;
                }
            }

            [$tGenerics, $tRefs] = self::parseTemplatesInDocblock($doc);
            $refs = array_merge($refs, $tRefs, self::getRefsInDocblock($doc));
            $generics = array_merge($generics, $tGenerics);
        }

        return array_filter($refs, function ($ref) use ($generics) {
            return ! in_array($ref['class'], $generics);
        });
    }

    private static function read(DocBlockFactory $docblock, $content, $lineNumber): DocBlock
    {
        return $docblock->create(
            $content,
            new Context('q1w23e4rt___ffff000'),
            new Location($lineNumber, 4)
        );
    }

    /*
     * This method corrects the following invalid docBlocks
     * @param Empty<>
     * @param array<mixed, string>
     * @var ?Test|?User
     */
    private static function normalizeDocblockContent(string $docblock)
    {
        $docblock = str_replace('mixed', 'string', $docblock);

        return str_replace(['?', '<>'], '', $docblock);
    }

    private static function addRef($refsInDocBlock, int $line, array $allRefs)
    {
        foreach ($refsInDocBlock as $ref) {
            $ref = str_replace('[]', '', $ref);
            $ref = trim($ref, '<>');
            $ref && self::shouldBeCollected($ref) && $allRefs[] = [
                'class' => str_replace('\\q1w23e4rt___ffff000\\', '', $ref),
                'line' => $line,
            ];
        }

        return $allRefs;
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

    private static function parseTemplatesInDocblock(DocBlock $docblock): array
    {
        if (! method_exists($docblock, 'getTags')) {
            return [[], []];
        }

        $line = $docblock->getLocation()->getLineNumber();
        $generics = self::extractGenericTags($docblock, $line);
        $refs = self::extractTemplateRefs($docblock, $line);

        return [$generics, $refs];
    }

    private static function extractGenericTags(DocBlock $docblock, int $line): array
    {
        $generics = [];

        foreach ($docblock->getTags() as $tag) {
            if (!$tag instanceof DocBlock\Tags\Generic) {
                continue;
            }
            $tagName = $tag->__toString();
            if (empty($tagName)) {
                continue;
            }
            $generics[] = explode(' of ', $tagName)[0];
        }

        return $generics;
    }

    private static function extractTemplateRefs(DocBlock $docblock, int $line): array
    {
        $refs = [];

        foreach ($docblock->getTags() as $tag) {
            if (!$tag instanceof DocBlock\Tags\Generic) {
                continue;
            }
            $tagName = $tag->__toString();
            if (empty($tagName)) {
                continue;
            }
            $partsOfName = explode(' of ', $tagName);
            if (!isset($partsOfName[1]) || in_array($partsOfName[1], self::$ignoreTemplateRefs)) {
                continue;
            }

            $refs[] = ['line' => $line, 'class' => $partsOfName[1]];
        }

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

    private static function readMethodTag(DocBlock $docblock, int $line)
    {
        $refs = [];

        foreach ($docblock->getTagsByName('method') as $method) {
            $refs = self::addRef(self::explode($method->getReturnType()), $line, $refs);

            $methodName = method_exists($method, 'getParameters') ? 'getParameters' : 'getArguments';
            foreach ($method->$methodName() as $argument) {
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
                $refs = self::findRefsTags($ref, $line, $refs);
            }

            return $refs;
        };
    }

    private static function findRefsTags($types, int $line, array $refs): array
    {
        if (! method_exists($types, 'getType') && ! method_exists($types, 'getValueType') && ! method_exists($types, 'getFqsen')) {
            return self::addRef(self::explode($types), $line, $refs);
        }

        if (method_exists($types, 'getFqsen')) {
            $refs = self::addRef(self::explode($types->getFqsen()), $line, $refs);
        }

        if (method_exists($types, 'getType') && ($type = $types->getType())) {
            return self::findRefsTags($type, $line, $refs);
        }

        if (method_exists($types, 'getValueType') && ($types = $types->getValueType())) {
            return self::findRefsTags($types, $line, $refs);
        }

        return $refs;
    }

    public static function explode($ref): array
    {
        $ref = str_replace(',', '|', (string) $ref);

        return explode('|', $ref);
    }

    private static function shouldBeCollected(string $ref)
    {
        return ! ClassReferenceFinder::isBuiltinType([0, $ref]) && ! Str::contains($ref, ['<', '>', '$', ':', '(', ')', '{', '}', '-']);
    }
}
