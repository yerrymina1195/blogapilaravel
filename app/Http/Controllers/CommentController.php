<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour commenter'], 401);
            }

            $validator = $this->validateComment($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            $comment = Comment::create([
                "content" => $data["content"],
                "user_Id" => $user->id,
                "article_Id" => $data["article_Id"]
            ]);

            return response()->json($comment, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update_comment(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            if (!$comment) {
                return response()->json(["message" => "Commentaire non trouvé"], 404);
            }

            if (!$this->canEditComment($comment)) {
                return response()->json(["message" => "Vous n'avez pas l'autorisation"], 401);
            }

            $validator = $this->validateComment($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $comment->update($data);

            return response()->json(["message" => "Modification faite"], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete_comment($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            if (!$comment) {
                return response()->json(["message" => "Commentaire non trouvé"], 404);
            }

            if (!$this->canDeleteComment($comment)) {
                return response()->json(["message" => "Vous n'avez pas l'autorisation"], 401);
            }

            $comment->delete();

            return response()->json(["message" => "Suppression faite avec succès"]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function validateComment($data)
    {
        return Validator::make($data, [
            'content' => 'required|string|max:255',
            'article_Id' => 'required|exists:articles,id'
        ], [
            'content.required' => 'Le contenu est obligatoire',
            'content.max' => 'Le contenu ne doit pas dépasser :max caractères',
            'article_Id.required' => 'Article non trouvé pour commenter',
            'article_Id.exists' => 'Article non trouvé pour commenter'
        ]);
    }

    protected function canEditComment($comment)
    {
        return Auth::check() && $comment->user_Id == Auth::id();
    }

    protected function canDeleteComment($comment)
    {
        $user = Auth::user();
        return $user && ($comment->user_id == $user->id || $comment->article->user_Id == $user->id);
    }
}
