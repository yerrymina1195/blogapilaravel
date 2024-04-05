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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->boolean('isArchived')->default(false);
            $table->unsignedBigInteger('category_Id');
            $table->foreign('category_Id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('user_Id');
            $table->foreign('user_Id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
