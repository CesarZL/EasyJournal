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
            $table->string('name');
            $table->string('father_surname');
            $table->string('mother_surname');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('country');
            $table->string('address');
            $table->string('institution');
            $table->string('affiliation')->nullable();
            $table->string('orcid')->unique();
            $table->text('biography')->nullable();
            $table->string('photo')->nullable();
            $table->string('scopus_id')->nullable();
            $table->string('researcher_id')->nullable();
            $table->string('url')->nullable();
            $table->string('affiliation_url')->nullable();
            $table->timestamps();
            // Foreign key related to the user who created this coauthor
            // $table->foreignId('created_by')->constrained('users');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

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
