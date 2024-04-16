<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = Article::query()
            ->where('name', 'like', "%{$search}%")
            ->with(['user', 'comments'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour créer un article'], 401);
            }
            $article = new Article();

            $validator = $article->validateArticle($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            $imagePath = $this->uploadImage($request->file('image'));

            $article = Article::create([
                'image' => $imagePath,
                'user_Id' => $user->id,
                'name' => $data['name'],
                'content' => $data['content'],
                'category_Id' => $data['category_Id']
            ]);

            return response()->json($article, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_article(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);

            $validator = $this->validateArticle($request->all(), $article->id);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('image')) {
                $this->deleteImage($article->image);
                $imagePath = $this->uploadImage($request->file('image'));
                $data['image'] = $imagePath;
            }

            $article->update($data);

            return response()->json(['data' => $article, 'message' => 'Modification faite'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $article = Article::with('user')->find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }
        return response()->json($article, 200);
    }

    public function delete_article($id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $this->deleteImage($article->image);
        $article->delete();

        return response()->json(['message' => 'Suppression effectuée'], 200);
    }

    // protected function validateArticle($data, $articleId = null)
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

    //     $messages = [
    //         'name.required' => 'Le titre est obligatoire',
    //         'name.max' => 'Le titre ne doit pas dépasser :max caractères',
    //         'name.unique' => 'Choisissez un autre titre, celui-ci existe déjà',
    //         'content.required' => 'Le contenu est obligatoire',
    //         'category_Id.required' => 'Choisissez une catégorie',
    //         'category_Id.exists' => 'Cette catégorie n\'existe pas',
    //         'image.image' => 'Le fichier doit être une image',
    //         'image.max' => 'La taille de l\'image ne doit pas dépasser 2 Mo',
    //     ];

    //     return Validator::make($data, $rules, $messages);
    // }

    protected function uploadImage($file)
    {
        if (!$file) {
            return null;
        }

        $fileName = $file->getClientOriginalName();
        $filePath = 'articles/' . $fileName;

        Storage::disk('public')->putFileAs('articles', $file, $fileName);

        return $filePath;
    }

    protected function deleteImage($path)
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
