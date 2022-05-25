<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ClassMethods;
use Imanghafoori\TokenAnalyzer\ClassReferenceFinder;

class AbstractMethodsClassTest extends BaseTestClass
{
    /** @test */
    public function check_final_class_is_detected2_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/arguments.stub'));

        [$output,] = ClassReferenceFinder::process($tokens);
           $this->assertEquals('T1', $output[0][0][1]);
           $this->assertEquals('T2', $output[1][0][1]);
           $this->assertEquals('T3', $output[2][0][1]);
           $this->assertEquals('T33', $output[3][0][1]);
           $this->assertEquals('T4', $output[4][0][1]);
           $this->assertEquals('T5', $output[5][0][1]);
           $this->assertEquals('T6', $output[6][0][1]);
           $this->assertEquals('T7', $output[7][0][1]);
           $this->assertEquals('T8', $output[8][0][1]);
    }

    /** @test */
    public function check_final_class_is_detected_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/final_class.stub'));
        $class = ClassMethods::read($tokens);

        $this->assertTrue($class['is_final']);
        $methods = $class['methods'];
        $this->assertCount(2, $methods);
        $this->assertEquals(true, $methods[0]['is_final']);
        $this->assertEquals(true, $methods[1]['is_final']);
    }

    /** @test */
    public function check_is_abstract_method_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/abstract_sample_class.stub'));
        $class = ClassMethods::read($tokens);
        $methods = $class['methods'];
        // Checks all the methods are abstract
        $this->assertEquals(true, $methods[0]['is_abstract']);
        $this->assertEquals(true, $methods[1]['is_abstract']);
        $this->assertEquals(true, $methods[2]['is_abstract']);
        $this->assertEquals(true, $methods[3]['is_abstract']);
        $this->assertEquals(true, $methods[4]['is_abstract']);
        $this->assertEquals(true, $methods[5]['is_abstract']);
        $this->assertEquals(true, $methods[6]['is_abstract']);
        $this->assertEquals(true, $methods[7]['is_abstract']);
        $this->assertEquals(true, $methods[8]['is_abstract']);
        $this->assertEquals(true, $methods[9]['is_abstract']);
        $this->assertEquals(true, $methods[10]['is_abstract']);
        $this->assertEquals(true, $methods[11]['is_abstract']);
        $this->assertEquals(true, $methods[12]['is_abstract']);
        $this->assertEquals(true, $methods[13]['is_abstract']);
        $this->assertEquals(true, $methods[14]['is_abstract']);
        $this->assertEquals(true, $methods[15]['is_abstract']);
        $this->assertEquals(true, $methods[16]['is_abstract']);
        $this->assertEquals(true, $methods[17]['is_abstract']);
        $this->assertEquals(true, $methods[18]['is_abstract']);
        $this->assertEquals(true, $methods[19]['is_abstract']);
        $this->assertEquals(true, $methods[20]['is_abstract']);
        $this->assertEquals(true, $methods[21]['is_abstract']);
        $this->assertEquals(true, $methods[22]['is_abstract']);
        $this->assertEquals(true, $methods[23]['is_abstract']);
        $this->assertEquals(true, $methods[24]['is_abstract']);
        $this->assertEquals(true, $methods[25]['is_abstract']);
        $this->assertEquals(false, $methods[26]['is_abstract']);
    }

    /** @test */
    public function check_return_types_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/abstract_sample_class.stub'));
        $class = ClassMethods::read($tokens);
        $methods = $class['methods'];
        // check is nullable return types
        $this->assertEquals(null, $methods[0]['nullable_return_type']);
        $this->assertEquals(false, $methods[6]['nullable_return_type']);
        $this->assertEquals(true, $methods[13]['nullable_return_type']);

        $this->assertEquals(null, $methods[0]['returnType']);
        $this->assertEquals('test', $methods[6]['returnType'][0][1]);
        $this->assertEquals('string', $methods[7]['returnType'][0][1]);
        $this->assertEquals('bool', $methods[8]['returnType'][0][1]);
        $this->assertEquals('int', $methods[9]['returnType'][0][1]);
        $this->assertEquals('array', $methods[10]['returnType'][0][1]);
        $this->assertEquals('void', $methods[11]['returnType'][0][1]);
        $this->assertEquals('float', $methods[12]['returnType'][0][1]);
        $this->assertEquals('string', $methods[13]['returnType'][0][1]);
    }

    /** @test */
    public function check_visibility_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/abstract_sample_class.stub'));
        $class = ClassMethods::read($tokens);
        $methods = $class['methods'];

        $this->assertEquals('public', $methods[0]['visibility'][1]);
        $this->assertEquals('public', $methods[1]['visibility'][1]);
        $this->assertEquals('protected', $methods[2]['visibility'][1]);
        $this->assertEquals('public', $methods[3]['visibility'][1]);
        $this->assertEquals('public', $methods[4]['visibility'][1]);
        $this->assertEquals('protected', $methods[5]['visibility'][1]);
        $this->assertEquals('public', $methods[6]['visibility'][1]);
        $this->assertEquals('public', $methods[7]['visibility'][1]);
        $this->assertEquals('public', $methods[8]['visibility'][1]);
        $this->assertEquals('public', $methods[9]['visibility'][1]);

        $this->assertEquals('public', $methods[22]['visibility'][1]);
        $this->assertEquals('public', $methods[23]['visibility'][1]);
        $this->assertEquals('protected', $methods[24]['visibility'][1]);
        $this->assertEquals('public', $methods[25]['visibility'][1]);
    }

    /** @test */
    public function check_is_static_method_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/abstract_sample_class.stub'));
        $class = ClassMethods::read($tokens);
        $methods = $class['methods'];

        $this->assertEquals(true, $methods[3]['is_static']);
        $this->assertEquals(true, $methods[4]['is_static']);
        $this->assertEquals(true, $methods[5]['is_static']);
        $this->assertEquals(true, $methods[25]['is_static']);
    }

    /** @test  */
    public function abstract_class_general_body_test()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/abstract_sample_class.stub'));
        $class = ClassMethods::read($tokens);

        $this->assertEquals([T_STRING, 'abstract_sample', 7], $class['name']);
        $this->assertCount(27, $class['methods']);
        $this->assertEquals(true, $class['is_abstract']);
        $this->assertEquals(T_CLASS, $class['type']);
    }

    /** @test */
    public function check_parameter_methods()
    {
        $tokens = token_get_all(file_get_contents(__DIR__.'/stubs/abstract_sample_class.stub'));
        $class = ClassMethods::read($tokens);
        $methods = $class['methods'];

        // check function has parameter
        $this->assertEquals('$parameter1', $methods[14]['signature'][0][1]);
        // check nullable type cast method parameters
        $this->assertEquals('?', $methods[15]['signature'][0]);
        $this->assertEquals('int', $methods[15]['signature'][1][1]);
        $this->assertEquals('$parameter1', $methods[15]['signature'][3][1]);
        // check type hinting of parameters
        $this->assertEquals('int', $methods[16]['signature'][0][1]);
        // number of parameter
        $signatures = $methods[17]['signature'];
        $parameters = array_filter($signatures, function ($item) {
            return is_array($item) && substr($item[1], 0, 1) == '$';
        });

        $this->assertCount(3, $parameters);
        // check multi parameter with type
        $this->assertEquals('...', $methods[18]['signature'][0][1]);
        $this->assertEquals('$parameter2', $methods[18]['signature'][1][1]);

        // check multi parameter with type casting
        $this->assertEquals('string', $methods[19]['signature'][0][1]);
        $this->assertEquals('...', $methods[19]['signature'][2][1]);
        $this->assertEquals('$parameter1', $methods[19]['signature'][3][1]);

        // check method with nullable multi parameter
        $this->assertEquals('?', $methods[20]['signature'][0]);
        $this->assertEquals('string', $methods[20]['signature'][1][1]);
        $this->assertEquals('...', $methods[20]['signature'][3][1]);
        $this->assertEquals('$parameter1', $methods[20]['signature'][4][1]);

        // check default value of parameters
        $this->assertEquals('$parameter1', $methods[21]['signature'][0][1]);
        $this->assertEquals('=', $methods[21]['signature'][2]);
        $this->assertEquals('null', $methods[21]['signature'][4][1]);
    }
}
