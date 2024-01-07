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

        $this->assertEquals( ["class" => "Eloquent", "line" => 6], $output[$i++]);
        $this->assertEquals( ["class" => "\App\Eloquent", "line" => 6], $output[$i++]);
        $this->assertEquals( ["class" => "A", "line" => 6], $output[$i++]);
        $this->assertEquals( ["class" => "Logger", "line" => 12], $output[$i++]);
        $this->assertEquals( ["class" => "Hello", "line" => 17], $output[$i++]);
        $this->assertEquals( ["class" => "Hello3", "line" => 17], $output[$i++]);
        $this->assertEquals( ["class" => "Hello2", "line" => 17], $output[$i++]);
        $this->assertEquals( ["class" => "ArrayIterator", "line" => 17], $output[$i++]);
        $this->assertEquals( ["class" => "Returny", "line" => 17], $output[$i++]);
        $this->assertEquals( ["class" => 'DOMElement', "line" => 17], $output[$i++]);
        $this->assertEquals( ["class" => "\Exception", "line" => 17], $output[$i++]);
        $this->assertEquals( ["class" => "User", "line" => 29], $output[$i++]);
        $this->assertEquals( ["class" => "Test", "line" => 34], $output[$i++]);
        $this->assertEquals( ["class" => "Products", "line" => 39], $output[$i++]);
        $this->assertEquals( ["class" => "Product", "line" => 39], $output[$i++]);
        $this->assertEquals( ["class" => "Collection", "line" => 39], $output[$i++]);
        $this->assertEquals( ["class" => "User", "line" => 39], $output[$i++]);
        $this->assertEquals( ["class" => "Collection", "line" => 39], $output[$i++]);
        $this->assertEquals( ["class" => "Test", "line" => 39], $output[$i++]);
        $this->assertEquals( ["class" => "User", "line" => 39], $output[$i++]);
        $this->assertEquals( ["class" => "Empty", "line" => 46], $output[$i++]);
        $this->assertEquals( ["class" => "MixArray", "line" => 46], $output[$i++]);
        $this->assertEquals( ["class" => "User", "line" => 46], $output[$i++]);
        $this->assertEquals( ["class" => "AbstractSchemaManager", "line" => 62], $output[$i++]);
        $this->assertEquals( ["class" => "SQLServerPlatform", "line" => 62], $output[$i++]);
        $this->assertEquals( ["class" => "MockObject", "line" => 62], $output[$i++]);
        $this->assertEquals( ["class" => "Generator", "line" => 62], $output[$i++]);

        $this->assertCount($i, $output);
    }

    /** @test */
    public function can_ignore_template_dockblocks()
    {
        $tokens = $this->getTokens(__DIR__ . '/stubs/docblocks/template.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);

        $excepted = [
            ['line' => 5, 'class' => '\Exception'],
        ];

        $this->assertEquals($excepted, $output);
        $this->assertCount(1, $output);
    }

    /** @test */
    public function can_detect_invalid_tags()
    {
        $tokens = $this->getTokens(__DIR__ . '/stubs/docblocks/invalid_tags.stub');
        $output = DocblockReader::readRefsInDocblocks($tokens);

        $excepted = [
            ['line' => 5, 'class' => 'DateTimeInterface'],
            ['line' => 16, 'class' => 'ColumnCase'],
        ];

        $this->assertEquals($excepted, $output);
    }
}
