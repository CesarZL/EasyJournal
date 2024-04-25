<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use AlAminFirdows\LaravelEditorJs\Facades\LaravelEditorJs;


class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'abstract',
        'keywords',
        'content',
        'bib',
        'user_id',
    ];

    
    public function getBodyAttribute()
    {
        return LaravelEditorJs::render($this->content);
    }

    public function getExcerptAttribute()
    {
        return substr(strip_tags($this->body), 0, 120);
    }

    public function getPublishedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }



    // Define the relationship between Article and User (Author)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship between Article and Coauthor
    public function coauthors()
    {
        return $this->belongsToMany(Coauthor::class, 'coauthor_article');
    }

    
    

}


