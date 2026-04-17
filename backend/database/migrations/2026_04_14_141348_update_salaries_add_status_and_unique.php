<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->decimal('tax', 12, 2)->default(0)->after('bonus');

            $table->enum('status', [
                'draft',
                'calculated',
                'approved',
                'published'
            ])->default('draft')->after('total');

            $table->unique(['employee_id', 'month', 'year'], 'unique_salary_per_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropUnique('unique_salary_per_month');

            $table->dropColumn(['status', 'tax']);
        });
    }
};