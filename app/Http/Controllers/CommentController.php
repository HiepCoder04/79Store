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
        ]);
        $user_id = Auth::user()->id;
        $data = [
            "product_id" => $re->product_id,
            "name" => $re->name,
            "email" => $re->email,
            "content" => $re->comment,
            "user_id" => $user_id,
        ];
        Comment::create($data);
        return redirect()->back();
    }
}
