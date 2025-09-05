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
        Schema::create('module_service_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('module_service_id')->nullable();
            $table->bigInteger('contract_person_id')->nullable();
            $table->bigInteger('machine_id')->nullable();
            $table->date('installation_date');
            $table->bigInteger('serial_number')->nullable();
            $table->date('supply_date');
            $table->foreignId('status_id')->nullable()->default(1)->constrained('statuses');
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
        Schema::dropIfExists('module_service_details');
    }
};
