<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
function uploadImage($file)
{
    if (!$file) {
        return null;
    }

    $fileName = $file->getClientOriginalName();
    $filePath = 'articles/' . $fileName;

    Storage::disk('public')->putFileAs('articles', $file, $fileName);

    return $filePath;
};



function deleteImage($path)
{
    if ($path) {
        Storage::disk('public')->delete($path);
    }
}



function canEdit($comment)
{
    return Auth::check() && $comment->user_Id == Auth::id();
}
 function canDelete($comment)
{
    $user = Auth::user();
    return $user && ($comment->user_id == $user->id || $comment->article->user_Id == $user->id);
}