<?php

use App\Models\Company\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saas_company_id')->nullable()->constrained('companies');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->string('name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->integer('total_employee')->nullable();
            $table->string('business_type')->nullable();
            $table->string('trade_licence_number')->nullable();
            $table->string('subdomain')->nullable();
            $table->foreignId('trade_licence_id')->nullable()->index('trade_licence_id')->constrained('uploads');
            $table->foreignId('status_id')->index('status_id')->default(1)->constrained('statuses');
            $table->enum('is_main_company', ['yes', 'no'])->default('no');
            $table->tinyInteger('is_subscription')->nullable()->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
