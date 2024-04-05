<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    //


    public function store(Request $request)
    {


        try {

            if (!Auth::check()) {
                return response()->json(['error' => 'Vous devez être connecté pour commenter'], 401);
            }

            $validator =   Validator::make($request->all(), [
                'content' => 'required|string|max:255',
                'article_Id' => 'required'
            ], $mesages = [
                'content.max' => 'le contenu ne doit pas depasser :max carcteres',
                'content.required' => 'le contenu est obligatoire',
                'article_Id.required' => 'artile non trouvé pour commenter'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $data = $validator->validated();

            $commentaire = Comment::create([
                "content" => $data["content"],
                "user_Id" => Auth::id(),
                "article_Id" => $data["article_Id"]
            ]);

            return response()->json($commentaire, 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function update_comment(Request $request, $id)
    {
        $commentaire = Comment::findOrFail($id);
        if (!$commentaire) {
            return response()->json(["message" => "commentaire non trouvé"], 404);
        }

        
        if (!Auth::check()) {
            return response()->json(['error' => 'Vous devez être connecté pour commenter'], 401);
        }

        if ($commentaire->user_Id == Auth::id()) {

            try {

                $validator =   Validator::make($request->all(), [
                    'content' => 'required|string|max:255',

                ], $mesages = [
                    'content.max' => 'le contenu ne doit pas depasser :max carcteres',
                    'content.required' => 'le contenu est obligatoire',

                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                $data = $validator->validated();
                $commentaire->update($data);

                return response()->json(["modification faite"], 201);
            } catch (Exception $e) {
                return response()->json($e);
            }
        }

        return response()->json(["message" => "vous n'avez l'autorisation"], 401);
    }

    public function delete_comment($id)
    {
        $commentaire = Comment::findOrFail($id);
        try {
            if ($commentaire->user_Id == Auth::id() || $commentaire->article->user_Id == Auth::id() ) {
                $commentaire->delete();

                return response()->json(["suppresion faite avec succes"]);
            }

            return response()->json(["message" => "vous n'avez l'autorisation"], 401);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
