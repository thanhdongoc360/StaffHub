<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    public function test_string_contains_substring()
    {
        $str = 'StaffHub - Employee Management';
        $this->assertStringContainsString('Employee', $str);
    }

    public function test_string_length()
    {
        $str = 'StaffHub';
        $this->assertEquals(7, strlen($str));
    }
}
