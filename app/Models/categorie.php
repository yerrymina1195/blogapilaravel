<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_Id'
    ];

    public function articles(){
        return $this->hasMany(article::class);
    }
    public function users(){
        return $this->belongsTo(User::class, 'user_Id');

    }

}
