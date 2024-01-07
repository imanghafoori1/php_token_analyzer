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
    public static function readRefsInDocblocks($tokens): array
    {
        ClassReferenceFinder::defineConstants();
        $docblock = DocBlockFactory::createInstance();

        $refs = [];
        $atTemplate = [];
        foreach ($tokens as $token) {
            if ($token[0] !== T_DOC_COMMENT) {
                continue;
            }
            try {
                [, $content, $line] = $token;
                /**
                 * Extends tag was replaced with var
                 * Because we don't have phpDocumentor\Reflection\Types\Collection in the extends tag
                 */
                $content = \str_replace('@extends', '@var', $content);
                $doc = self::read($docblock, \str_replace('?', '', $content), $line);
            } catch (RuntimeException $e) {
                try {
                    $doc = self::read($docblock, self::normalizeDocblockContent($content), $line);
                } catch (RuntimeException $e) {
                    continue;
                }
            }

            [$tTemplates, $tRefs] = self::parseTemplatesInDocblock($doc);
            $refs = \array_merge($refs, $tRefs, self::getRefsInDocblock($doc));
            $atTemplate = \array_merge($atTemplate, $tTemplates);
        }

        // use array_values for reset array keys.
        return \array_values(
            \array_filter($refs, function ($ref) use ($atTemplate) {
                return ! \in_array($ref['class'], $atTemplate);
            })
        );
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
        $docblock = \str_replace('mixed', 'string', $docblock);

        return \str_replace(['?', '<>'], '', $docblock);
    }

    private static function addRef($refsInDocBlock, int $line, array $allRefs)
    {
        foreach ($refsInDocBlock as $ref) {
            $ref = \str_replace('[]', '', $ref);
            $ref = \trim($ref, '<>');
            // For parse this "ColumnCase::LOWER"
            $ref = \strtok($ref, '::');
            $ref && self::shouldBeCollected($ref) && $allRefs[] = [
                'class' => \str_replace('\\q1w23e4rt___ffff000\\', '', $ref),
                'line' => $line,
            ];
        }

        return $allRefs;
    }

    private static function getRefsInDocblock(DocBlock $docblock): array
    {
        $line = $docblock->getLocation()->getLineNumber();

        $readRef = self::getRefReader($docblock, $line);

        return \array_merge(
            self::readDescriptionRefs($docblock, $line),
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
        if (! \method_exists($docblock, 'getTags')) {
            return [[], []];
        }

        $line = $docblock->getLocation()->getLineNumber();
        $atTemplate = self::extractTemplateTags($docblock, $line);
        $refs = self::extractTemplateRefs($docblock, $line);

        return [$atTemplate, $refs];
    }

    private static function extractTemplateTags(DocBlock $docblock, int $line): array
    {
        $atTemplate = [];

        foreach ($docblock->getTags() as $tag) {
            if ($tag->getName() !== 'template') {
                continue;
            }
            $tagName = $tag->__toString();
            if (empty($tagName)) {
                continue;
            }
            $atTemplate[] = \explode(' of ', $tagName)[0];
        }

        return $atTemplate;
    }

    private static function extractTemplateRefs(DocBlock $docblock, int $line): array
    {
        $refs = [];

        foreach ($docblock->getTags() as $tag) {
            if ($tag->getName() !== 'template') {
                continue;
            }
            $tagName = $tag->__toString();
            if (empty($tagName)) {
                continue;
            }
            $partsOfName = \explode(' of ', $tagName);
            if (! isset($partsOfName[1]) || ! self::shouldBeCollected($partsOfName[1])) {
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
            if (!$desc) {
                continue;
            }

            $mixins[] = [
                'line' => $line,
                'class' => \strtok($desc->__toString(), ' '),
            ];
        }

        return $mixins;
    }

    private static function readMethodTag(DocBlock $docblock, int $line): array
    {
        $refs = [];

        foreach ($docblock->getTagsByName('method') as $method) {
            $refs = self::addRef(self::explode($method->getReturnType()), $line, $refs);

            $methodName = \method_exists($method, 'getParameters') ? 'getParameters' : 'getArguments';
            foreach ($method->$methodName() as $argument) {
                $_refs = self::explode(\str_replace('?', '', (string) $argument['type']));
                $refs = self::addRef($_refs, $line, $refs);
            }
        }

        return $refs;
    }

    private static function getRefReader(DocBlock $docblock, int $line): Closure
    {
        return function ($tagName) use ($docblock, $line): array {
            $refs = [];
            foreach ($docblock->getTagsByName($tagName) as $ref) {
                $refs = self::findRefsTags($ref, $line, $refs);
            }

            return $refs;
        };
    }

    private static function readDescriptionRefs(DocBlock $docblock, int $line): array
    {
        $description = $docblock->getDescription();

        $refs = [];
        if (\method_exists($description, 'getTags')) {
            foreach ($docblock->getDescription()->getTags() as $tag) {
                $refs = self::findRefsTags($tag, $line, $refs);
            }
        }

        return $refs;
    }

    private static function findRefsTags($types, int $line, array $refs): array
    {
        if (! \method_exists($types, 'getType')
            && ! \method_exists($types, 'getValueType')
            && ! \method_exists($types, 'getFqsen')) {
            return self::addRef(self::explode($types), $line, $refs);
        }

        if (\method_exists($types, 'getFqsen')) {
            $refs = self::addRef(self::explode($types->getFqsen()), $line, $refs);
        }

        if (\method_exists($types, 'getType') && ($type = $types->getType())) {
            return self::findRefsTags($type, $line, $refs);
        }

        if (\method_exists($types, 'getValueType') && ($types = $types->getValueType())) {
            return self::findRefsTags($types, $line, $refs);
        }

        return $refs;
    }

    public static function explode($ref): array
    {
        $ref = \str_replace([',', '&'], '|', (string) $ref);

        return \explode('|', $ref);
    }

    private static function shouldBeCollected(string $ref): bool
    {
        return ! ClassReferenceFinder::isBuiltinType([0, $ref])
            && ! Str::contains($ref, ['<', '>', '$', ':', '(', ')', '{', '}', '-']);
    }
}
