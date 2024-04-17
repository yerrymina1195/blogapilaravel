<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public static function validatedTag($data)
    {
        $rules = [
            'name' => 'required|min:3|unique:tags,name',
        ];

        $messages = [
            'name.required' => 'Le nom est obligatoire',
            'name.max' => 'Le nom ne doit pas dépasser :max caractères',
            'name.unique' => 'Le nom doit être unique',
        ];

        return Validator::make($data, $rules, $messages);
    }

    public function articles()
    {
        return $this->belongsToMany(article::class, 'tag_articles', 'tag_id', 'article_id');
    }
}
