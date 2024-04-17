<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour commenter'], 401);
            }

            $validator = Comment::validatedComment($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data["user_Id"]=$user->id;
            $comment = Comment::create($data);

            return response()->json(["success"=>true,"data"=>$comment], 200);
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

            if (!canEdit($comment)) {
                return response()->json(["message" => "Vous n'avez pas l'autorisation"], 401);
            }

            $validator = Comment::validatedComment($request->all());
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
            $comment = Comment::find($id);
            if (!$comment) {
                return response()->json(["message" => "Commentaire non trouvé"], 404);
            }

            if (!canDelete($comment)) {
                return response()->json(["message" => "Vous n'avez pas l'autorisation"], 401);
            }

            $comment->delete();

            return response()->json(["message" => "Suppression faite avec succès"]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
