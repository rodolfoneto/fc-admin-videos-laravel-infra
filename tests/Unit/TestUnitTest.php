<?php

namespace Tests\Unit;

use Core\Test;
use PHPUnit\Framework\TestCase;

class TestUnitTest extends TestCase
{
    public function test_call_method_foo()
    {
        $test = new Test();
        $this->assertEquals('bee', $test->foo());
    }
}
