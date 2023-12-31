<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class TypeHintedPropertiesTest extends BaseTestClass
{
    public function setUp(): void
    {
        parent::setUp();

        if (! version_compare(phpversion(), '7.4.0', '>=')) {
            $this->markTestSkipped('Your php version is less than 7.4');
        }
    }

    /** @test */
    public function type_hinted_property()
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
        $this->assertCount(0, $attributeRefs);
    }

    /** @test */
    public function can_detect_arrow_functions_test()
    {
        $tokens = $this->getTokens(__DIR__.'/stubs/php74/arrow_functions.stubs');
        [$classRefs, $namespace, $attributeRefs,] = ClassReferenceFinder::process($tokens);

        $this->assertEquals('T4', $classRefs[0][0][1]);
        $this->assertEquals('T5', $classRefs[1][0][1]);
        $this->assertEquals('T6', $classRefs[2][0][1]);
        $this->assertEquals('T7', $classRefs[3][0][1]);
        $this->assertEquals("H", $classRefs[4][0][1]);
        $this->assertEquals("T", $classRefs[5][0][1]);
        $this->assertCount(0, $attributeRefs);
    }
}


