<?php

namespace App\Http\Controllers;

use App\Models\article;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    //

    public function index()
    {


        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');


        $data = article::query()->where('name', 'like', "%{$search}%")->with(['user', 'comments'])->orderBy($sortField, $sortDirection)->paginate($perPage);
        return response()->json($data, 200);
    }


    public function store ( Request $request)
    {
        try {
            
            if (!Auth::check()) {
                return response()->json(['error' => 'Vous devez être connecté pour creer un article'], 401);
            }

            $validator=   Validator::make($request->all(),[
                'name'=> 'required|max:50|unique:articles,name',
                'content'=>'required',
                'category_Id'=>'required|exists:categories,id'
    
            ],$mesages =[
                'name.required'=> 'le titre est obligatoire',
                'name.max' => 'le titre ne dpit pas depasser :max carcteres',
                'name.unique' => 'choisissez un autre titre celui ci existe deja',
                'content.required'=>'le contenu est obligatoire',
                'category_Id.required'=> 'choisssez une categorie',
                'category_Id.exists'=>'ce categorie n\'existe pas ',
            ]);
            if ($validator->fails()){
                return response()->json(['errors'=> $validator->errors()],422);
            }
            $data= $validator->validated();

            $filePath = null; 
    
            if ($request->hasFile('image')) {
                $file= $request->file('image');
                $name=$file->getClientOriginalName();
                $filename= $name;
                // $path= 'storage/articles/' . $filename;
                // if(File::exists($path)){
                //     File::delete($path);
                //     return response()->json(["ht"=>'supprimer'],203);
                // }
    
                try{
    
                    $filePath = 'articles/' . $filename;
    
                    $file->storeAs('articles', $filename, 'public');
                    $fullUrl = Storage::disk('public')->url($filePath);
                    
                }catch(Exception $e){
                    dd($e);
                }
            }
                $datas = article::create([
                    'image'=> $filePath,
                    'user_Id'=> Auth::id(),
                    'name'=>$data['name'],
                    'content'=>$data['content'],
                    'category_Id'=>$data['category_Id']
                ]);
                return response()->json($datas,200);
        } catch (Exception $e) {
            return response()->json($e,500);
        }
   
    }

    public function show($id)
    {
        $articles= article::with('user')->find($id);

        if(!$articles){
            return response()->json(["message"=>'not found'],404);
        }

        return response()->json($articles,200);
    }

    public function delete_article($id)
    {
        $Article= article::find($id);
        if(!$Article){
            return response()->json(["message"=>'article non trouvé'],404);
        }
        $Article->delete();
        return response()->json("suppression faite");
    }

    public function update_article(Request $request, $id)
{

    $articles= Article::findOrFail($id);

        if(!$articles){
            return response()->json(["message"=>'not found'],404);
        }

    try {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:50', Rule::unique('articles')->ignore($articles->id)],
            'content' => 'required',
            'category_Id' => 'required|exists:categories,id'
        ], [
            'name.required' => 'Le titre est obligatoire',
            'name.max' => 'Le titre ne doit pas dépasser :max caractères',
            'name.unique' => 'Choisissez un autre titre, celui-ci existe déjà',
            'content.required' => 'Le contenu est obligatoire',
            'category_Id.required' => 'Choisissez une catégorie',
            'category_Id.exists' => 'Cette catégorie n\'existe pas'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = $file->getClientOriginalName();
            $filename = $name;

            $path= 'storage/articles/' . $filename;
                if(File::exists($path)){
                    File::delete($path);
                }

            try {
                $filePath = 'articles/' . $filename;
                $file->storeAs('articles', $filename, 'public');
                $data['image'] = $filePath;
            } catch (Exception $e) {
                return response()->json(['error' => 'Erreur lors du chargement de l\'image'], 500);
            }
        }

        $articles->update([
            'image' => $data['image'] ?? $articles->image,
            'name' => $data['name'],
            'content' => $data['content'],
            'category_Id' => $data['category_Id']
        ]);

        return response()->json(["data"=>$articles,"message"=>"modification faite"], 200);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


// public function update_article(Request $request, $id)
// {
//     $article = Article::findOrFail($id);

//     if (!$article) {
//         return response()->json(["message" => 'not found'], 404);
//     }

//     try {
//         $validator = Validator::make($request->all(), [
//             'name' => ['required', 'max:50', Rule::unique('articles')->ignore($article->id)],
//             'content' => 'required',
//             'category_Id' => 'required|exists:categories,id'
//         ], [
//             'name.required' => 'Le titre est obligatoire',
//             'name.max' => 'Le titre ne doit pas dépasser :max caractères',
//             'name.unique' => 'Choisissez un autre titre, celui-ci existe déjà',
//             'content.required' => 'Le contenu est obligatoire',
//             'category_Id.required' => 'Choisissez une catégorie',
//             'category_Id.exists' => 'Cette catégorie n\'existe pas'
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()], 422);
//         }

//         $data = $validator->validated();

//         if ($request->hasFile('image')) {
//             $file = $request->file('image');
//             $name = $file->getClientOriginalName();
//             $filename = $name;

//             $path = 'storage/articles/' . $filename;
//             if (File::exists($path)) {
//                 File::delete($path);
//             }

//             try {
//                 $filePath = 'articles/' . $filename;
//                 $file->storeAs('articles', $filename, 'public');
//                 $article->image = $filePath;
//             } catch (Exception $e) {
//                 return response()->json(['error' => 'Erreur lors du chargement de l\'image'], 500);
//             }
//         }

//         $article->name = $data['name'];
//         $article->content = $data['content'];
//         $article->category_Id = $data['category_Id'];

//         $article->save();

//      return response()->json(["data"=>$article,"message"=>"modification faite"], 200);
//     } catch (Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }


}
