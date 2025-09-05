<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_expense_categories', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Accounts', 'Travel'])->nullable()->default('Accounts');
            $table->string('name', 191);
            $table->tinyInteger('is_income')->default(0)->comment('0=Expense, 1=Income');
            $table->foreignId('attachment_file_id')->nullable()->constrained('uploads');
            $table->foreignId('status_id')->index('status_id')->default(1)->constrained('statuses');
            $table->timestamps();

            //modified on 10 Nov 2023
            $table->unsignedBigInteger('company_id')->nullable()->default(1);
            $table->unsignedBigInteger('branch_id')->nullable()->default(1);
            $table->index(['company_id', 'branch_id']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if(isModuleActive('Travel')) {
            $categories = [
                [
                    'type' => 'Travel',
                    'name' => 'Lodging',
                    'is_income' => 0,
                    'attachment_file_id' => null,
                    'status_id' => 1,
                    'company_id' => 1,
                    'branch_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'Travel',
                    'name' => 'Food',
                    'is_income' => 0,
                    'attachment_file_id' => null,
                    'status_id' => 1,
                    'company_id' => 1,
                    'branch_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'Travel',
                    'name' => 'Entertainment',
                    'is_income' => 0,
                    'attachment_file_id' => null,
                    'status_id' => 1,
                    'company_id' => 1,
                    'branch_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'Travel',
                    'name' => 'Others',
                    'is_income' => 0,
                    'attachment_file_id' => null,
                    'status_id' => 1,
                    'company_id' => 1,
                    'branch_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('income_expense_categories')->insert($categories);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income_expense_categories');
    }
}
