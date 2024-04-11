<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coauthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'last_name',
        'email',
        'phone',
        'address',
        'institution',
        'country',
        'ORCID',
        'scopus_id',
        'researcher_id',
        'author_id',
        'url',
        'affiliation',
        'affiliation_url',
        'article_id',
        'created_by'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname} {$this->last_name}";
    }

    public function getFullInstitutionAttribute()
    {
        return "{$this->institution} {$this->affiliation}";
    }


}


// $table->id();
// $table->unsignedBigInteger('article_id');
// $table->string('name');
// $table->string('surname');
// $table->string('last_name');
// $table->string('email');
// $table->string('phone');
// $table->string('address');
// $table->string('institution');
// $table->string('country');
// $table->string('ORCID')->nullable();
// $table->string('scopus_id')->nullable();
// $table->string('researcher_id')->nullable();
// $table->string('author_id')->nullable();
// $table->string('url')->nullable();
// $table->string('affiliation')->nullable();
// $table->string('affiliation_url')->nullable();
// $table->timestamps();

// $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
// $table->unsignedBigInteger('created_by');
// $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');