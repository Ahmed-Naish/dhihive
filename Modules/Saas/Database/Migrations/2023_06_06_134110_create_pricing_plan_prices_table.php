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
        if (!Schema::hasTable('pricing_plan_prices'))
        Schema::create('pricing_plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained('pricing_plans')->cascadeOnDelete();
            $table->foreignId('plan_duration_type_id')->constrained('plan_duration_types')->cascadeOnDelete();
            $table->decimal('price')->nullable()->default(0);
            $table->bigInteger('status_id')->default(1)->comment('1=active,4=inactive');
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['id', 'status_id', 'company_id', 'branch_id']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $pricingPlanPrices = [
            ['pricing_plan_id' => 1, 'plan_duration_type_id' => 1, 'price' => 9],
            ['pricing_plan_id' => 1, 'plan_duration_type_id' => 2, 'price' => 24],
            ['pricing_plan_id' => 1, 'plan_duration_type_id' => 3, 'price' => 49],
            ['pricing_plan_id' => 1, 'plan_duration_type_id' => 4, 'price' => 99],

            ['pricing_plan_id' => 2, 'plan_duration_type_id' => 1, 'price' => 29],
            ['pricing_plan_id' => 2, 'plan_duration_type_id' => 2, 'price' => 79],
            ['pricing_plan_id' => 2, 'plan_duration_type_id' => 3, 'price' => 149],
            ['pricing_plan_id' => 2, 'plan_duration_type_id' => 4, 'price' => 299],

            ['pricing_plan_id' => 3, 'plan_duration_type_id' => 1, 'price' => 99],
            ['pricing_plan_id' => 3, 'plan_duration_type_id' => 2, 'price' => 249],
            ['pricing_plan_id' => 3, 'plan_duration_type_id' => 3, 'price' => 499],
            ['pricing_plan_id' => 3, 'plan_duration_type_id' => 4, 'price' => 899],

            ['pricing_plan_id' => 4, 'plan_duration_type_id' => 1, 'price' => 199],
            ['pricing_plan_id' => 4, 'plan_duration_type_id' => 2, 'price' => 499],
            ['pricing_plan_id' => 4, 'plan_duration_type_id' => 3, 'price' => 999],
            ['pricing_plan_id' => 4, 'plan_duration_type_id' => 4, 'price' => 1999],
        ];

        if (env('APP_24hourworx')) {
            $pricingPlanPrices[] = ['pricing_plan_id' => 1, 'plan_duration_type_id' => 5, 'price' => 0];
            $pricingPlanPrices[] = ['pricing_plan_id' => 2, 'plan_duration_type_id' => 5, 'price' => 0];
            $pricingPlanPrices[] = ['pricing_plan_id' => 3, 'plan_duration_type_id' => 5, 'price' => 0];
            $pricingPlanPrices[] = ['pricing_plan_id' => 4, 'plan_duration_type_id' => 5, 'price' => 0];

            $pricingPlanPrices[] = ['pricing_plan_id' => 5, 'plan_duration_type_id' => 1, 'price' => 0];
            $pricingPlanPrices[] = ['pricing_plan_id' => 5, 'plan_duration_type_id' => 2, 'price' => 0];
            $pricingPlanPrices[] = ['pricing_plan_id' => 5, 'plan_duration_type_id' => 3, 'price' => 0];
            $pricingPlanPrices[] = ['pricing_plan_id' => 5, 'plan_duration_type_id' => 4, 'price' => 0];
            $pricingPlanPrices[] = ['pricing_plan_id' => 5, 'plan_duration_type_id' => 5, 'price' => 0];
        }

        foreach ($pricingPlanPrices as $pricingPlanPrice) {
            DB::table('pricing_plan_prices')->insert([
                'pricing_plan_id' => $pricingPlanPrice['pricing_plan_id'],
                'plan_duration_type_id' => $pricingPlanPrice['plan_duration_type_id'],
                'price' => $pricingPlanPrice['price'],
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
        Schema::dropIfExists('pricing_plan_prices');
    }
};
