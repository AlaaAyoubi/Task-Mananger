<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            //ربط المهمة بالفريق
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            //ربط المهمة بعضو (قد يكون مدير أو عضو عادي)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // الحالة: pending, in_progress, completed, canceled
            $table->string('status')->default(config('constants.task_statuses.pending'));

            // الأولوية: high, medium, low
            $table->string('priority')->default('medium');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
