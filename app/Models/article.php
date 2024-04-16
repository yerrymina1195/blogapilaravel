<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public static $rules = [
     
            'name' => 'required|max:50|unique:articles,name,',
            'content' => 'required',
            'category_Id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048', // 2MB limit
        
    ];

    // public function validateArticle($data, $articleId = null)
    // {
    //     $rules = [
    //         'name' => [
    //             'required',
    //             'max:50',
    //             Rule::unique('articles')->ignore($articleId),
    //         ],
    //         'content' => 'required',
    //         'category_Id' => 'required|exists:categories,id',
    //         'image' => 'nullable|image|max:2048', // 2MB limit
    //     ];

    //     return Validator::make($data, $rules);
    // }

  


    public static $messages = [

            'name.required' => 'Le titre est obligatoire',
            'name.max' => 'Le titre ne doit pas dépasser :max caractères',
            'name.unique' => 'Choisissez un autre titre, celui-ci existe déjà',
            'content.required' => 'Le contenu est obligatoire',
            'category_Id.required' => 'Choisissez une catégorie',
            'category_Id.exists' => 'Cette catégorie n\'existe pas',
            'image.image' => 'Le fichier doit être une image',
           
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
