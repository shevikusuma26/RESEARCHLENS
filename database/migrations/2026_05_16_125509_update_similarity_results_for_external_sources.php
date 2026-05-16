<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('similarity_results', function (Blueprint $table) {
            $table->foreignId('research_source_id')->nullable()->after('compared_project_id')->constrained('research_sources')->onDelete('cascade');
            $table->decimal('novelty_score', 5, 2)->default(0)->after('method_similarity');
            
            // Drop unique constraint if it exists (project_id, compared_project_id)
            $table->dropUnique(['project_id', 'compared_project_id']);
        });

        // Use raw SQL to make compared_project_id nullable to avoid doctrine/dbal requirement
        DB::statement('ALTER TABLE similarity_results MODIFY compared_project_id BIGINT UNSIGNED NULL');

        Schema::table('similarity_results', function (Blueprint $table) {
            // Add new unique index for project + research source
            $table->unique(['project_id', 'research_source_id'], 'project_research_unique');
        });
    }

    public function down()
    {
        Schema::table('similarity_results', function (Blueprint $table) {
            $table->dropUnique('project_research_unique');
        });

        DB::statement('ALTER TABLE similarity_results MODIFY compared_project_id BIGINT UNSIGNED NOT NULL');

        Schema::table('similarity_results', function (Blueprint $table) {
            $table->unique(['project_id', 'compared_project_id']);
            $table->dropColumn(['research_source_id', 'novelty_score']);
        });
    }
};
