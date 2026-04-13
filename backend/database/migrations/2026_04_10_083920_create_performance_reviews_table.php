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
            Schema::create('performance_reviews', function (Blueprint $table) {
                $table->id();

                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

                // ky danh gia
                $table->integer('period_month');
                $table->integer('period_year');

                // diem (0-100)
                $table->integer('kpi_score')->nullable();
                $table->integer('discipline_score')->nullable();
                $table->integer('collaboration_score')->nullable();
                $table->integer('growth_score')->nullable();

                // Tong
                $table->float('total_score')->nullable();
                $table->string('rank')->nullable();

                // nhan xet
                $table->text('kpi_comment')->nullable();
                $table->text('discipline_comment')->nullable();
                $table->text('collaboration_comment')->nullable();
                $table->text('reviewer_comment')->nullable();

                $table->enum('status', ['draft', 'submitted', 'confirmed'])->default('draft');

                $table->timestamps();

                $table->unique(['employee_id', 'period_month', 'period_year']);
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('performance_reviews');
        }
    };
