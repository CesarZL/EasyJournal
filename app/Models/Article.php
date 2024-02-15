<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = ['name', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

// Schema::create('article', function (Blueprint $table) {
//     $table->id();
//     //guardar el nombre del articulo
//     $table->string('name');
//     // guardar json
//     $table->json('content')->nullable();
//     // relacionar con usuario, un usuario puede tener muchos articulos
//     $table->foreignId('user_id')->constrained();
//     $table->softDeletes();
//     $table->timestamps();
// });
