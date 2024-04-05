<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class article extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'image',
        'content',
        'isArchived',
        'category_Id',
        'user_Id',
    ];


    public function category ()
     {
      return  $this->belongsTo(categorie::class);
    }


    public function user(){
        return $this->belongsTo(User::class, 'user_Id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'article_Id');
    }
}
