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
        Schema::create('service_package_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->nullable()->constrained('service_machines')->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained('service_brands')->onDelete('cascade');
            $table->foreignId('model_id')->nullable()->constrained('service_models')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('service_packages')->onDelete('cascade');
            $table->string('origin')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('warranty_period')->nullable();
            $table->integer('status_id')->nullable()->default(1);

            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['id', 'status_id', 'company_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_package_details');
    }
};
