<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_project_id')->constrained('final_projects')->onDelete('cascade');
            $table->string('keyword');
            $table->timestamps();

            $table->index('final_project_id');
            $table->index('keyword');
        });
    }

    public function down()
    {
        Schema::dropIfExists('keywords');
    }
};
