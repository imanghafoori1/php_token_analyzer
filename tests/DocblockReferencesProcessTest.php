<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\DocblockReader;

class DocblockReferencesProcessTest extends BaseTestClass
{
    /** @test */
    public function can_detect_docblocks()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/docblocks/doc_block_ref.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);
        $i = 0;

        $this->assertEquals(['class' => 'Eloquent', 'line' => 6], $output[$i++]);
        $this->assertEquals(['class' => '\App\Eloquent', 'line' => 6], $output[$i++]);
        $this->assertEquals(['class' => 'A', 'line' => 6], $output[$i++]);
        $this->assertEquals(['class' => 'Logger', 'line' => 12], $output[$i++]);
        $this->assertEquals(['class' => 'Hello', 'line' => 17], $output[$i++]);
        $this->assertEquals(['class' => 'Hello3', 'line' => 17], $output[$i++]);
        $this->assertEquals(['class' => 'Hello2', 'line' => 17], $output[$i++]);
        $this->assertEquals(['class' => 'ArrayIterator', 'line' => 17], $output[$i++]);
        $this->assertEquals(['class' => 'Returny', 'line' => 17], $output[$i++]);
        $this->assertEquals(['class' => 'DOMElement', 'line' => 17], $output[$i++]);
        $this->assertEquals(['class' => '\Exception', 'line' => 17], $output[$i++]);
        $this->assertEquals(['class' => 'User', 'line' => 29], $output[$i++]);
        $this->assertEquals(['class' => 'Test', 'line' => 34], $output[$i++]);
        $this->assertEquals(['class' => 'Products', 'line' => 39], $output[$i++]);
        $this->assertEquals(['class' => 'Product', 'line' => 39], $output[$i++]);
        $this->assertEquals(['class' => 'Collection', 'line' => 39], $output[$i++]);
        $this->assertEquals(['class' => 'User', 'line' => 39], $output[$i++]);
        $this->assertEquals(['class' => 'Collection', 'line' => 39], $output[$i++]);
        $this->assertEquals(['class' => 'Test', 'line' => 39], $output[$i++]);
        $this->assertEquals(['class' => 'User2', 'line' => 39], $output[$i++]);
        //$this->assertEquals(['class' => 'MixArray', 'line' => 46], $output[$i++]);
        //$this->assertEquals(['class' => 'User', 'line' => 46], $output[$i++]);
        $this->assertEquals(['class' => 'AbstractSchemaManager', 'line' => 62], $output[$i++]);
        $this->assertEquals(['class' => 'SQLServerPlatform', 'line' => 62], $output[$i++]);
        $this->assertEquals(['class' => 'MockObject', 'line' => 62], $output[$i++]);
        $this->assertEquals(['class' => 'Generator', 'line' => 62], $output[$i++]);
        $this->assertEquals(['class' => 'ColumnCase', 'line' => 67], $output[$i++]);
        $this->assertEquals(['class' => 'ColumnCase', 'line' => 67], $output[$i++]);
        $this->assertEquals(['class' => 'Statement', 'line' => 70], $output[$i++]);
        $this->assertEquals(['class' => 'Cat', 'line' => 70], $output[$i++]);
        $this->assertEquals(['class' => 'Yellow', 'line' => 70], $output[$i++]);
        $this->assertEquals(['class' => 'LaraCast', 'line' => 70], $output[$i++]);
        $this->assertEquals(['class' => 'User3', 'line' => 79], $output[$i++]);

        $this->assertCount($i, $output);
    }

    /**
     * @test
     */
    public function see_tag()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/docblocks/doc_block_ref_see.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);
        $this->assertEquals([], $output);
    }

    /** @test */
    public function can_ignore_template_dockblocks()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/docblocks/template.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);

        $excepted = [
            ['line' => 5, 'class' => '\Exception'],
        ];

        $this->assertEquals($excepted, $output);
        $this->assertCount(1, $output);
    }

    /** @test */
    public function can_ignore_builtin_types_and_special_keywords()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/docblocks/ignore_builtin_types.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);

        $expected = [
            ['class' => 'MyValidClass', 'line' => 5],
            ['class' => '\RuntimeException', 'line' => 5],
            ['class' => 'AnotherClass', 'line' => 5],
        ];

        $this->assertEquals($expected, $output);
    }

    /** @test */
    public function sanitizes_variable_names_and_handles_property_and_method_tags()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/docblocks/sanitize_and_more_tags.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);

        $expected = [
            ['class' => 'User', 'line' => 5],
            ['class' => 'Product', 'line' => 5],
            ['class' => 'QueryBuilder', 'line' => 5],
        ];

        sort($expected);
        sort($output);

        $this->assertEquals($expected, $output);
    }
}
