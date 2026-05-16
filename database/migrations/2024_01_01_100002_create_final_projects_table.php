<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('final_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->longText('abstract');
            $table->longText('research_method')->nullable();
            $table->string('proposal_file')->nullable();
            $table->decimal('novelty_score', 5, 2)->default(0);
            $table->decimal('similarity_score', 5, 2)->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('category_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('final_projects');
    }
};
