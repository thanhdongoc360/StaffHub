<?php

namespace Tests\Unit;

use Tests\TestCase;

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
        $this->assertEquals(8, strlen($str));
    }
}
