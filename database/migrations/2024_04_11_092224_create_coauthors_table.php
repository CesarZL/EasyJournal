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
        Schema::create('coauthors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id')->nullable();
            $table->string('name');
            $table->string('surname');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('institution');
            $table->string('country');
            $table->string('ORCID')->nullable();
            $table->string('scopus_id')->nullable();
            $table->string('researcher_id')->nullable();
            $table->string('author_id')->nullable();
            $table->string('url')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('affiliation_url')->nullable();
            $table->timestamps();
        
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coauthors');
    }
};
