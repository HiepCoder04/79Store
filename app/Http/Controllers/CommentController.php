<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $re)
    {
        
        $re->validate([
            'product_id' => 'required|exists:products,id',
            'comment' => 'required|string|max:3000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
        $user_id = Auth::user()->id;
        $data = [
            "product_id" => $re->product_id,
            "name" => $re->name,
            "email" => $re->email,
            "content" => $re->comment,
            "user_id" => $user_id,
            'parent_id' => $re->parent_id,
            'is_admin' => Auth::user()->is_admin ?? false,
        ];
        Comment::create($data);
        return redirect()->back();
    }
}
