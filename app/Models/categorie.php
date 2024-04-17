<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_Id'
    ];


    public static function validatedCategory($data)
    {
        $rules = [
            'title' => 'required|max:50|unique:categories,title',
        ];

        $messages = [
            'title.required' => 'Le titre est obligatoire',
            'title.max' => 'Le titre ne doit pas dépasser :max caractères',
            'title.unique' => 'Le titre doit être unique',
        ];

        return Validator::make($data, $rules, $messages);
    }



    public function articles(){
        return $this->hasMany(article::class);
    }
    public function users(){
        return $this->belongsTo(User::class, 'user_Id');

    }

}
