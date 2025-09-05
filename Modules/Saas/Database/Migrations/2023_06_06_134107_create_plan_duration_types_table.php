<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Saas\Enums\PricingPlanDurationType;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('plan_duration_types'))
        Schema::create('plan_duration_types', function (Blueprint $table) {
            $table->id();
            $table->enum('name', [PricingPlanDurationType::MONTHLY, PricingPlanDurationType::QUARTERLY, PricingPlanDurationType::HALF_YEARLY, PricingPlanDurationType::YEARLY, PricingPlanDurationType::LIFETIME]);
            $table->bigInteger('status_id')->default(1)->comment('1=active,4=inactive');
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['id', 'status_id', 'company_id', 'branch_id']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $durationTypes = [PricingPlanDurationType::MONTHLY, PricingPlanDurationType::QUARTERLY, PricingPlanDurationType::HALF_YEARLY, PricingPlanDurationType::YEARLY, PricingPlanDurationType::LIFETIME];

        foreach ($durationTypes as $durationType) {
            DB::table('plan_duration_types')->insert([
                'name' => $durationType,
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
        Schema::dropIfExists('plan_duration_types');
    }
};
