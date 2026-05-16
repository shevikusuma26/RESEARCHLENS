<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_project_id')->constrained('final_projects')->onDelete('cascade');
            $table->longText('recommendation_text');
            $table->string('recommendation_type'); // feature, technology, method, innovation
            $table->timestamps();

            $table->index('final_project_id');
            $table->index('recommendation_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('recommendations');
    }
};
