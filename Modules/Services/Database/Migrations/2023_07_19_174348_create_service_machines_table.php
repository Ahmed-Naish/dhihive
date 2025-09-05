<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Services\Database\Seeders\ServiceMachineTableSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_machines', function (Blueprint $table) {
            $table->id();
            $table->string('machine_name');
            $table->foreignId('brand_id')->nullable()->constrained('service_brands')->onDelete('cascade');
            $table->foreignId('model_id')->nullable()->constrained('service_models')->onDelete('cascade');
            $table->string('origin');
            $table->integer('status_id')->nullable()->default(1);
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['id', 'status_id', 'company_id', 'branch_id']);
        });

        $seeder = new ServiceMachineTableSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_machines');
    }
};
