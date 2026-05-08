<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;

class UserModelTest extends TestCase
{
    public function test_fillable_contains_expected_fields()
    {
        $user = new User();
        $expected = ['name','email','password','role'];
        foreach ($expected as $field) {
            $this->assertContains($field, $user->getFillable());
        }
    }

    public function test_relations_return_relation_instances()
    {
        $user = new User();
        $this->assertInstanceOf(Relation::class, $user->employee());
        $this->assertInstanceOf(Relation::class, $user->notifications());
        $this->assertInstanceOf(Relation::class, $user->performanceReviews());
    }

    public function test_role_attribute_returns_set_value_when_no_relation()
    {
        $user = new User(['role' => 'manager']);
        $this->assertEquals('manager', $user->role);
    }
}
