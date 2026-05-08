<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\Relation;

class EmployeeModelTest extends TestCase
{
    public function test_fillable_contains_expected_fields()
    {
        $employee = new Employee();
        $expected = ['user_id','employee_code','position','department','phone','status'];
        foreach ($expected as $field) {
            $this->assertContains($field, $employee->getFillable());
        }
    }

    public function test_relationship_methods_return_relations()
    {
        $employee = new Employee();
        $this->assertInstanceOf(Relation::class, $employee->user());
        $this->assertInstanceOf(Relation::class, $employee->leaveRequest());
        $this->assertInstanceOf(Relation::class, $employee->salaries());
        $this->assertInstanceOf(Relation::class, $employee->attendances());
    }
}
