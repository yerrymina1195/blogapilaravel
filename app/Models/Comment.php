<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
    'content',
    'user_Id',
    'article_Id'];



    public static function validatedComment($data)
    {
        $rules = [
            'content' => 'required|string|max:255',
            'article_Id' => 'required|exists:articles,id'
        ];

        $messages = [
            'content.required' => 'Le contenu est obligatoire',
            'content.max' => 'Le contenu ne doit pas dépasser :max caractères',
            'article_Id.required' => 'Article non trouvé pour commenter',
            'article_Id.exists' => 'Article non trouvé pour commenter'
        ];


        return Validator::make($data, $rules, $messages);
    }



    public function user(){
        return $this->belongsTo(User::class, 'user_Id');
    }
    public function article(){
        return $this->belongsTo(article::class, 'article_Id');
    }
}
