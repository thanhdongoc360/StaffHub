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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->date('date');

            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();

            $table->enum('status', [
                'present',
                'late',
                'absent',
                'half_day'
            ])->default('absent');

            $table->integer('working_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);

            $table->text('note')->nullable();
            
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
