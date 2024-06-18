<?php

namespace Imanghafoori\TokenAnalyzer\Tests;

use Imanghafoori\TokenAnalyzer\ExistenceChecker;

class ExistenceCheckTest extends BaseTestClass
{
    public function test_existence_checker()
    {
        $result = ExistenceChecker::check(self::class, __DIR__.'/ExistenceCheckTest.php');
        $this->assertTrue($result);

        $result = ExistenceChecker::check('svf', __DIR__.'/ExistenceCheckTest.php');
        $this->assertFalse($result);

        // count function of php
        $result = ExistenceChecker::check('count', __DIR__.'/ExistenceCheckTest.php');
        $this->assertTrue($result);
    }
}