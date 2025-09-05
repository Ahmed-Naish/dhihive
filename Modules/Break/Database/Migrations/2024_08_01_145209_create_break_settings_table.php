<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('break_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->longText('encrypt_text')->nullable();
            $table->longText('encrypt_code')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();
        });

        // if (session()->has('input') && env('APP_MOOD') == 'Saas') {
        //     DB::table('break_settings')->insert([
        //         'company_id' => 1,
        //         'branch_id' => 1,
        //         'title' => 'Break',
        //         'encrypt_text' => 'hrm.imprintdhaka.com',
        //         'encrypt_code' => encrypt('hrm.imprintdhaka.com'),
        //         'path' => 'assets/images/qr.svg',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('break_settings');
    }
};
