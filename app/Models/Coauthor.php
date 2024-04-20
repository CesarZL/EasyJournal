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
        'orcid',
        'scopus_id',
        'researcher_id',
        'url',
        'affiliation',
        'affiliation_url',
        'biography',
        'created_by',
    ];

    // Define the relationship between Coauthor and Article
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    

}

