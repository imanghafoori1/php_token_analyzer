<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class Php74SyntaxTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (! version_compare(phpversion(), '7.4.0', '>=')) {
            $this->markTestSkipped('Your php version is less than 7.4');
        }
    }

    public function test_type_hinted_property()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/type_hinted_property.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('', $namespace);
        $this->assertEquals('tH0', $classRefs[0][0][1]);
        $this->assertEquals('tH1', $classRefs[1][0][1]);
        $this->assertEquals('t\H2', $classRefs[2][0][1]);
        $this->assertEquals('\tH3', $classRefs[3][0][1]);
        $this->assertEquals('tH4', $classRefs[4][0][1]);
        $this->assertEquals('tH5', $classRefs[5][0][1]);
        $this->assertEquals('\tH7\tH8', $classRefs[6][0][1]);
        $this->assertEquals('tH9', $classRefs[7][0][1]);
        $this->assertCount(8, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }

    public function test_can_detect_arrow_functions_test()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php74/arrow_functions.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);
        $i = 0;

        $this->assertEquals('T4', $classRefs[$i++][0][1]);
        $this->assertEquals('T5', $classRefs[$i++][0][1]);
        $this->assertEquals('T6', $classRefs[$i++][0][1]);
        $this->assertEquals('T7', $classRefs[$i++][0][1]);
        $this->assertEquals('H', $classRefs[$i++][0][1]);
        $this->assertEquals('T', $classRefs[$i++][0][1]);
        $this->assertCount($i, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }

    public function test_can_detect_arrow_functions_in_array()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php74/arrow_functions_in_array.stub');
        [$classRefs, $namespace, $attributeRefs] = ClassReferenceFinder::process($tokens);

        $expected = [
            [
                [T_STRING, 'Exception', 5],
            ],
            [
                [T_STRING, 'M', 8],
            ],
            [
                [T_STRING, 'L', 8],
            ],
            [
                [T_STRING, 'N', 8],
            ],
            [
                [T_STRING, 'N23', 8],
            ],
            [
                [T_STRING, 'Sn', 17],
            ],
        ];

        $this->assertEquals($expected, $classRefs);
        $this->assertCount(0, $attributeRefs);
    }
}
