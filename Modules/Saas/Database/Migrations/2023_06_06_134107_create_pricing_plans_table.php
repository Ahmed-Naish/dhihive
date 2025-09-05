<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (!Schema::hasTable('pricing_plans'))
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('employee_limit')->nullable()->default(0);
            $table->tinyInteger('is_employee_limit')->default(1)->comment('if 1 then employee create have some limit which is define in employee_limit column. If 0 then employee create have no limit.');
            $table->decimal('basic_price')->nullable()->default(0)->comment('This basic price count for 1 month');
            $table->boolean('is_popular')->nullable()->comment('if 1 is true then show popular badge otherwise not show');
            $table->bigInteger('status_id')->default(1)->comment('1=active,4=inactive');
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['id', 'status_id', 'company_id', 'branch_id']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $pricingPlans = [
            ['name' => 'Basic', 'employee_limit' => 10, 'is_employee_limit' => 1, 'basic_price' => 9, 'is_popular' => 0],
            ['name' => 'Standard', 'employee_limit' => 50, 'is_employee_limit' => 1, 'basic_price' => 29, 'is_popular' => 1],
            ['name' => 'Premium', 'employee_limit' => 500, 'is_employee_limit' => 1, 'basic_price' => 99, 'is_popular' => 0],
            ['name' => 'Enterprise', 'employee_limit' => 0, 'is_employee_limit' => 0, 'basic_price' => 199, 'is_popular' => 0],
        ];

        if (env('APP_24hourworx')) {
            $pricingPlans[] = ['name' => 'Lifetime', 'employee_limit' => 0, 'is_employee_limit' => 0, 'basic_price' => 0, 'is_popular' => 0];
        }

        foreach ($pricingPlans as $plan) {
            DB::table('pricing_plans')->insert([
                'name' => $plan['name'],
                'employee_limit' => $plan['employee_limit'],
                'is_employee_limit' => $plan['is_employee_limit'],
                'basic_price' => $plan['basic_price'],
                'is_popular' => $plan['is_popular'],
                'status_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricing_plans');
    }
};
