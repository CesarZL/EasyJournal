<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    //relacion de usuario a articulos
    public function article()
    {
        return $this->hasMany(Article::class);
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