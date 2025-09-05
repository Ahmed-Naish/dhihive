<?php

use App\Models\Upload;
use Modules\Break\Entities\BreakType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Break\Database\Seeders\BreakTypesTableSeeder;

class CreateBreakTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('break_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();

            // is remark required?
            $table->boolean('is_remark_required')->default(false);
            $table->boolean('will_ask_next_meal')->default(false)->comment('Will ask next meal?');
            // status
            $table->foreignId('status_id')->constrained('statuses')->onDelete('cascade');

            // limit
            $table->integer('limit')->nullable();
            $table->enum('limit_type', ['day', 'week', 'month', 'year'])->default('day');

            // duration
            $table->enum('duration_type', ['minute', 'hour'])->default('minute');
            $table->integer('max_duration')->nullable();

            $table->foreignId('icon_id')->nullable()->constrained('uploads');

            // company and branch
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');

            $table->unsignedBigInteger('created_by')->nullable()->default(1);
            $table->unsignedBigInteger('updated_by')->nullable()->default(1);


            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('break_types');
    }
}
