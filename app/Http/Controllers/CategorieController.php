<?php

namespace App\Http\Controllers;

use App\Models\categorie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    //


    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');

        $data = categorie::query()
            ->where('title', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return response()->json($data, 200);
    }


    public function store(Request $request)
    {
        try {

            $validator = categorie::validatedCategory($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $cate = $validator->validated();
            $cate['user_Id'] = Auth::id();
            $data = categorie::create($cate);

            return response()->json([
                'message' => 'categorie added successfully',
                'success' => true,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $Category = categorie::with('users')->find($id);

        if (!$Category) {
            return response()->json(['message' => 'categorie non trouvé'], 404);
        }

        return response()->json($Category);
    }

    public function delete_category($id)
    {
        $Category = categorie::find($id);
        if ($Category) {
            $Category->delete();
            return response()->json('Category delete');
        }
        return response()->json(['message' => 'Category not found'], 404);
    }

    public function update_category(Request $request, $id)
    {
        try {
            $category = categorie::find($id);
            if (!$category) {
                return response()->json(['message' => 'category not found'], 404);
            }

            $validator = categorie::validatedCategory($request->all());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            $category->update($data);

            return response()->json(['message' => 'category updated successfully', $data], 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }


    // public function update_category(Request $request,$id)
    // {
    //     try {
    //         $category = categorie::find($id);
    //         if (!$category) {
    //             return response()->json(['message' => 'category not found'], 404);
    //         }

    //         // $data = $request->validate([
    //         //     'title' => 'required|unique:categories,title'
    //         // ]);
    //         $data =  Validator::make($request->all(), [
    //             'title' => 'required|max:50',
    //         ], $messages = [
    //             'title' => "Le titre est obligatoire",
    //     ]);




    //         $category->update($data);

    //         return response()->json(['message' => 'category updated successfully'], 200);
    //     } catch (Exception $e) {
    //         return response()->json($e, 500);
    //     }
    // }
}
