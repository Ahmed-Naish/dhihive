<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('jm_user_preferences', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Notification type relationship
            $table->foreignId('notification_type_id')->constrained()->onDelete('cascade');

            // Preference type: 'enabled' or 'disabled'
            $table->enum('preference', ['enabled', 'disabled'])->default('enabled');

            // Status relationship (active/inactive)
            $table->foreignId('status_id')->constrained('statuses')->default(1)
                ->comment('1=active, 4=inactive');

            // Company and branch relationships (if applicable)
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('branch_id')->nullable()->constrained();

            // Additional metadata or notes
            $table->text('note')->nullable()->comment('Additional notes or details');

            // Timestamps for tracking creation and updates
            $table->timestamps();

            // Unique constraint to ensure each user can have only one preference per notification type
            $table->unique(['user_id', 'notification_type_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jm_user_preferences');
    }
};
