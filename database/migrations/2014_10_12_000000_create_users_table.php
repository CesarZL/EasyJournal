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
            $table->string('father_surname');
            $table->string('mother_surname');
            $table->string('phone')->unique();
            $table->string('country');
            $table->string('address')->nullable();
            $table->string('orcid')->unique()->nullable();
            $table->text('biography')->nullable();
            $table->string('institution');
            $table->string('affiliation')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
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


// $table->string('email')->unique();
// $table->string('password');
// $table->string('first_name');
// $table->string('last_name');
// $table->string('phone');
// $table->string('country');
// $table->string('address');
// $table->string('orcid')->nullable();
// $table->text('biography')->nullable();
// $table->string('photo')->nullable();