<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Sử dụng raw SQL để drop unique constraint nếu tồn tại
        // Bỏ qua error nếu constraint không tồn tại
        try {
            DB::statement('ALTER TABLE users DROP INDEX users_role_unique');
        } catch (\Exception $e) {
            // Ignore - constraint không tồn tại
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('role');
        });
    }
};
