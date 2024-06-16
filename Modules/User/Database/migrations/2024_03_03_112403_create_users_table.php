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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('active')->default(1);
            $table->boolean('blocked')->default(0);
            $table->string('address')->nullable();
            $table->foreignId('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreignId('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->timestamp('password_updated_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
