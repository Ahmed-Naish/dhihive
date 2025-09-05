<?php

use App\Models\Settings\Language;
use Illuminate\Support\Facades\Schema;
use App\Repositories\LanguageRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('languages'))
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('native')->nullable();
            $table->tinyInteger('rtl')->default('0')->nullable();
            $table->tinyInteger('status')->default('1')->comment('1=active, 0=inactive')->nullable();
            $table->tinyInteger('json_exist')->default('0')->nullable();
            $table->tinyInteger('is_default')->default('0')->nullable();

            // extra for saas
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index([ 'status', 'company_id', 'branch_id']);

        });

        $languageRepository = app(LanguageRepository::class);
        $languages          = $languageRepository->getLanguageData();

        foreach ($languages as $language) {
            Language::create(
                [
                    'code'   => $language[1],
                    'name'   => $language[2],
                    'native' => $language[3],
                    'rtl'    => $language[4],
                    'status' => $language[5],
                ]
            );
        }

        $english = Language::find(19);
        $english->status = 1;
        $english->is_default = 1;
        $english->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
