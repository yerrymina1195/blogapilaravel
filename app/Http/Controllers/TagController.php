<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function store(Request $request)
    {
        try {
            $validator = Tag::validatedTag($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $data = $validator->validated();
            $tag = Tag::create($data);

            return response()->json([
                'success' => true,
                'data' => $tag
            ], 201);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function delete($id)
    {
        $tag = Tag::find($id);
        if ($tag) {
            $tag->delete();
            return response()->json(['message' => 'tag delete']);
        }
        return response()->json(['message' => 'tag not found'], 404);
    }



    public function show($id)
    {

        $tag = Tag::find($id);
        if ($tag) {

            $articles = $tag->articles()->paginate(10);
            return response()->json(['tag' => $tag, 'articles' => $articles], 200);
        }
        return response()->json(['message' => 'tag not found'], 404);
    }


    public function  update(Request $request, $id)
    {
        try {

            $tag = Tag::find($id);
            if (!$tag) {
                return response()->json(['message' => 'tag not found'], 404);
            }
            $validator = Tag::validatedTag($request->all());
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $data = $validator->validated();

            $tag->update($data);
            return response()->json(['message' => 'tag updated successfully', 'data'=> $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }
}
