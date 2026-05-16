<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('api_key')->unique();
            $table->string('name')->default('Default Key');
            $table->string('status')->default('active'); // active, inactive
            $table->bigInteger('request_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('api_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_keys');
    }
};
