<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('similarity_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('final_projects')->onDelete('cascade');
            $table->foreignId('compared_project_id')->constrained('final_projects')->onDelete('cascade');
            $table->decimal('similarity_percentage', 5, 2);
            $table->string('analysis_type')->default('comprehensive'); // title, abstract, keyword, method
            $table->timestamps();

            $table->index('project_id');
            $table->index('compared_project_id');
            $table->unique(['project_id', 'compared_project_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('similarity_results');
    }
};
