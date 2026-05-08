<?php

namespace Tests\Unit;

use Tests\TestCase;

class ArrayTest extends TestCase
{
    public function test_array_has_key()
    {
        $arr = ['id' => 1, 'name' => 'Alice'];
        $this->assertArrayHasKey('name', $arr);
    }

    public function test_array_count()
    {
        $arr = [1,2,3];
        $this->assertCount(3, $arr);
    }
}
