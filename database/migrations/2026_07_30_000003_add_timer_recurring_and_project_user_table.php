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
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('timer_started_at')->nullable()->after('actual_hours');
            $table->string('recurring_frequency')->nullable()->after('timer_started_at'); // 'daily', 'weekly', 'monthly'
        });

        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        // Seed existing project owners into project_user pivot table
        $projects = \Illuminate\Support\Facades\DB::table('projects')->get();
        foreach ($projects as $p) {
            if ($p->created_by) {
                \Illuminate\Support\Facades\DB::table('project_user')->insertOrIgnore([
                    'project_id' => $p->id,
                    'user_id' => $p->created_by,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['timer_started_at', 'recurring_frequency']);
        });
    }
};
