<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('research_sources', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable()->index(); // Semantic Scholar ID / DOI
            $table->text('title');
            $table->text('abstract')->nullable();
            $table->string('authors')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('source_name')->nullable(); // Journal, IEEE, etc.
            $table->string('source_url')->nullable();
            $table->text('keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('research_sources');
    }
};
