<?php

namespace App\Http\Requests;

use App\Models\article;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // public function rules(): array
    // {
        
    //     return [
    //         'name' => 'required|max:50|unique:articles,name,',
    //         'content' => 'required',
    //         'category_Id' => 'required|exists:categories,id',
    //         'image' => 'nullable|image|max:2048', // 2MB limit
    //     ];
    // }
    public function rules(): array
    {
        
        return article::$rules;
    }
}

