<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add student_id and phone to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('student_id');
            }
        });

        // Add status to final_projects
        Schema::table('final_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('final_projects', 'status')) {
                $table->enum('status', ['draft', 'submitted', 'analyzed'])->default('draft')->after('similarity_score');
            }
        });

        // Add detail columns to similarity_results
        Schema::table('similarity_results', function (Blueprint $table) {
            if (!Schema::hasColumn('similarity_results', 'title_similarity')) {
                $table->decimal('title_similarity', 5, 2)->default(0)->after('similarity_percentage');
            }
            if (!Schema::hasColumn('similarity_results', 'abstract_similarity')) {
                $table->decimal('abstract_similarity', 5, 2)->default(0)->after('title_similarity');
            }
            if (!Schema::hasColumn('similarity_results', 'keyword_similarity')) {
                $table->decimal('keyword_similarity', 5, 2)->default(0)->after('abstract_similarity');
            }
            if (!Schema::hasColumn('similarity_results', 'method_similarity')) {
                $table->decimal('method_similarity', 5, 2)->default(0)->after('keyword_similarity');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_id', 'phone']);
        });

        Schema::table('final_projects', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('similarity_results', function (Blueprint $table) {
            $table->dropColumn(['title_similarity', 'abstract_similarity', 'keyword_similarity', 'method_similarity']);
        });
    }
};
