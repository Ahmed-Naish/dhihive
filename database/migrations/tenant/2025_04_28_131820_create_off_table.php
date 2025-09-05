<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffTable extends Migration
{
    public function up()
    {
        Schema::create('off', function (Blueprint $table) {
            $table->id(); 
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->unsignedBigInteger('em_id'); 
            $table->tinyInteger('day')->check('day BETWEEN 1 AND 7'); 
            $table->timestamps();


            $table->foreign('em_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('off');
    }
}
