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
        if (!Schema::hasTable('pricing_plan_features'))
        Schema::create('pricing_plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained('pricing_plans')->cascadeOnDelete();
            $table->foreignId('plan_feature_id')->constrained('plan_features')->cascadeOnDelete();
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['id', 'company_id', 'branch_id']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $pricingPlanFeatures = [
            ['pricing_plan_id' => 1, 'plan_feature_id' => 1],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 2],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 3],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 4],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 5],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 6],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 8],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 12],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 13],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 14],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 15],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 16],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 17],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 18],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 19],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 20],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 21],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 22],
            ['pricing_plan_id' => 1, 'plan_feature_id' => 23],

            ['pricing_plan_id' => 2, 'plan_feature_id' => 1],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 2],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 3],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 4],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 5],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 6],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 7],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 8],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 12],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 13],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 14],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 15],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 16],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 17],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 18],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 19],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 20],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 21],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 22],
            ['pricing_plan_id' => 2, 'plan_feature_id' => 23],

            ['pricing_plan_id' => 3, 'plan_feature_id' => 1],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 2],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 3],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 4],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 5],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 6],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 7],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 8],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 9],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 10],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 11],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 12],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 13],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 14],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 15],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 16],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 17],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 18],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 19],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 20],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 21],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 22],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 23],
            ['pricing_plan_id' => 3, 'plan_feature_id' => 24],

            ['pricing_plan_id' => 4, 'plan_feature_id' => 1],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 2],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 3],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 4],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 5],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 6],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 7],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 8],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 9],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 10],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 11],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 12],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 13],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 14],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 15],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 16],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 17],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 18],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 19],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 20],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 21],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 22],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 23],
            ['pricing_plan_id' => 4, 'plan_feature_id' => 24],
        ];

        if (env('APP_24hourworx')) {
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 1];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 2];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 3];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 4];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 5];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 6];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 7];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 8];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 9];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 10];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 11];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 12];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 13];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 14];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 15];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 16];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 17];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 18];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 19];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 20];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 21];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 22];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 23];
            $pricingPlanFeatures[] = ['pricing_plan_id' => 5, 'plan_feature_id' => 24];
        }

        foreach ($pricingPlanFeatures as $pricingPlanFeature) {
            DB::table('pricing_plan_features')->insert([
                'pricing_plan_id' => $pricingPlanFeature['pricing_plan_id'],
                'plan_feature_id' => $pricingPlanFeature['plan_feature_id'],
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
        Schema::dropIfExists('pricing_plan_features');
    }
};
