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
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();

            $table->time('work_start_time')->default('08:00:00');
            $table->time('work_end_time')->default('17:00:00');

            $table->integer('late_threshold_minutes')->default(15);
            $table->integer('half_day_threshold_minutes')->default(240);

            $table->integer('standard_work_minutes')->default(480);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_rules');
    }
};
