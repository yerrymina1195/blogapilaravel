<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $article = new Article();

            $validator = $article->validateArticle($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            $imagePath = uploadImage($request->file('image'));

            $data['image']=$imagePath;
            $data['user_Id']= $user->id;
            $article = Article::create($data);

            return response()->json([
                'message' => 'article added successfully',
                'success' => true,
                'data' => $article
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_article(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
            $articleInstance = new Article();

            $validator = $articleInstance->validateArticle($request->all(), $article->id);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('image')) {
                deleteImage($article->image);
                $imagePath = uploadImage($request->file('image'));
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

        deleteImage($article->image);
        $article->delete();

        return response()->json(['message' => 'Suppression effectuée'], 200);
    }
}
