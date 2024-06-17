<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\FileManipulator;

class FileManipulatorTest extends BaseTestClass
{
    public function test_removeLine()
    {
        $tmp = __DIR__.DIRECTORY_SEPARATOR.'tmp';
        $testFile = $tmp.DIRECTORY_SEPARATOR.'test.txt';

        ! is_dir($tmp) && mkdir($tmp);
        $file = fopen($tmp.DIRECTORY_SEPARATOR.'test.txt', "w");
        fwrite($file, 'L1'.PHP_EOL.'L2'.PHP_EOL.'L3'.PHP_EOL.PHP_EOL);
        fclose($file);

        //==============================
        //
        //==============================
        $result = FileManipulator::removeLine($testFile, 4);
        $this->assertTrue($result);
        $this->assertEquals([
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
            'L3'.PHP_EOL,
        ], file($testFile));
        //==============================
        //
        //==============================
        $result = FileManipulator::removeLine($testFile, 3);
        $this->assertTrue($result);
        $this->assertEquals([
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
        ], file($testFile));
        //==============================
        //
        //==============================
        $result = FileManipulator::removeLine($testFile, 3);
        $this->assertFalse($result);
        $this->assertEquals([
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
        ], file($testFile));
    }

    public function test_insertAtLine()
    {
        $tmp = __DIR__.DIRECTORY_SEPARATOR.'tmp';
        $testFile = $tmp.DIRECTORY_SEPARATOR.'test.txt';

        ! is_dir($tmp) && mkdir($tmp);
        $file = fopen($tmp.DIRECTORY_SEPARATOR.'test.txt', "w");
        fwrite($file, 'L1'.PHP_EOL.'L2'.PHP_EOL.'L3'.PHP_EOL.PHP_EOL);
        fclose($file);
        //==============================
        //
        //==============================
        $result = FileManipulator::insertAtLine($testFile, 'newLine',0);
        $this->assertFalse($result);
        $this->assertEquals([
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
            'L3'.PHP_EOL,
            PHP_EOL,
        ], file($testFile));
        //==============================
        //
        //==============================
        $result = FileManipulator::insertAtLine($testFile, 'newLine',1);
        $this->assertTrue($result);
        $this->assertEquals([
            'newLine'.PHP_EOL,
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
            'L3'.PHP_EOL,
            PHP_EOL,
        ], file($testFile));
        //==============================
        //
        //==============================
        $result = FileManipulator::insertAtLine($testFile, 'newLine',6);
        $this->assertTrue($result);
        $this->assertEquals([
            'newLine'.PHP_EOL,
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
            'L3'.PHP_EOL,
            PHP_EOL,
            'newLine'.PHP_EOL,
        ], file($testFile));
        //==============================
        //
        //==============================
        $result = FileManipulator::insertAtLine($testFile, 'newLine',8);
        $this->assertFalse($result);
        $this->assertEquals([
            'newLine'.PHP_EOL,
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
            'L3'.PHP_EOL,
            PHP_EOL,
            'newLine'.PHP_EOL,
        ], file($testFile));
    }

    public function test_replaceFirst()
    {
        $tmp = __DIR__.DIRECTORY_SEPARATOR.'tmp';
        $testFile = $tmp.DIRECTORY_SEPARATOR.'test.txt';

        ! is_dir($tmp) && mkdir($tmp);
        $file = fopen($tmp.DIRECTORY_SEPARATOR.'test.txt', "w");
        fwrite($file, 'L1'.PHP_EOL.'L2'.PHP_EOL.'L2'.PHP_EOL.PHP_EOL);
        fclose($file);
        //==============================
        //
        //==============================
        $result = FileManipulator::replaceFirst($testFile, 'old','new');
        $this->assertFalse($result);
        $this->assertEquals([
            'L1'.PHP_EOL,
            'L2'.PHP_EOL,
            'L2'.PHP_EOL,
            PHP_EOL,
        ], file($testFile));
        //==============================
        //
        //==============================
        $result = FileManipulator::replaceFirst($testFile, 'L2','newL2');
        $this->assertTrue($result);
        $this->assertEquals([
            'L1'.PHP_EOL,
            'newL2'.PHP_EOL,
            'L2'.PHP_EOL,
            PHP_EOL,
        ], file($testFile));
        //==============================
        //
        //==============================
        $result = FileManipulator::replaceFirst($testFile, 'L2','');
        $this->assertTrue($result);
        $this->assertEquals([
            'L1'.PHP_EOL,
            'new'.PHP_EOL,
            'L2'.PHP_EOL,
            PHP_EOL,
        ], file($testFile));
    }


    protected function tearDown(): void
    {
        $tmp = __DIR__.DIRECTORY_SEPARATOR.'tmp';
        $testFile = $tmp.DIRECTORY_SEPARATOR.'test.txt';

        is_file($testFile) && unlink($testFile);
        is_dir($tmp) && rmdir($tmp);
    }
}
