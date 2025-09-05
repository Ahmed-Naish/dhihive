<?php

use App\Models\Settings\Currency;
use Illuminate\Support\Facades\Schema;
use App\Repositories\CurrencyRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateCurrenciesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('currencies'))
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('symbol')->nullable()->collation('utf8mb4_unicode_ci');
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['company_id', 'branch_id']);
        });

        $currencyRepository = app(CurrencyRepository::class);
        $currencies         = $currencyRepository->getCurrencyData();

        foreach ($currencies as $currency) {
            $store = new Currency();

            $store->id     = $currency[0];
            $store->name   = $currency[1];
            $store->code   = $currency[2];
            $store->symbol = $currency[3];
            $store->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
