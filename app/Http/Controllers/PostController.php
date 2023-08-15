<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Get all posts
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
                })
                ->get(),
        ], 200);
    }

    // get single post

    public function show($id)
    {
        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')->get(),
        ], 200);
    }

    // create a post 

    public function store(Request $request)
    {

        $attrs = $request->validate([
            'body' => 'required|string',
        ]);

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'message' => 'post created successfully',
            'post' => $post,
        ], 200);
    }

    // update a post 
    public function update(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        if ($post->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Permission denied',
            ], 403);
        }

        $attrs = $request->validate([
            'body' => 'required|string',
        ]);

        $post->update([
            'body' => $attrs['body'],
        ]);

        return response()->json([
            'message' => 'Post updated',
            'post' => $post,
        ], 200);
    }

    // delete a post
    public function destroy($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        if ($post->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Permission denied',
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }
}
