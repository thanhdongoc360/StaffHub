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
        Schema::create('attendance_exceptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('handled_by')->constrained('users');

            $table->timestamp('old_check_in')->nullable();
            $table->timestamp('old_check_out')->nullable();

            $table->text('reason')->nullable();

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
        Schema::dropIfExists('attendance_exceptions');
    }
};
