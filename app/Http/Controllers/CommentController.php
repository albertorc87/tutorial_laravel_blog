<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        $request->validated();

        $user = Auth::user();
        $post = Post::find($request->input('post_id'));

        $comment = new Comment;
        $comment->comment = $request->input('comment');
        $comment->user()->associate($user);
        $comment->post()->associate($post);

        $res = $comment->save();

        if ($res) {
            return back()->with('status', 'Comment has been created sucessfully');
        }

        return back()->withErrors(['msg', 'There was an error saving the comment, please try again later']);
    }
}
