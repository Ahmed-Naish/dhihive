<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Break\Database\Seeders\UserBreaksTableSeeder;

class CreateUserBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('user_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('break_type_id')->constrained('break_types')->cascadeOnDelete();

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('duration')->default('00:00:00')->nullable();
            $table->string('reason')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);

            $table->unsignedBigInteger('created_by')->nullable()->default(1);
            $table->unsignedBigInteger('updated_by')->nullable()->default(1);

            $table->index(['company_id', 'branch_id']);

        });

        // $this->runSeeder();
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_breaks');
    }


    public function runSeeder()
    {
        $seeder = new UserBreaksTableSeeder();
        $seeder->run();
    }
}
