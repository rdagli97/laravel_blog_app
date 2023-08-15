<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    // like or unlike
    public function likeOrUnlike($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $like = $post->likes()->where('user_id', auth()->user()->id)->first();

        if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id,
            ]);

            return response()->json([
                'message' => 'Liked',
            ], 200);
        }

        $like->delete();

        return response()->json([
            'message' => 'Disliked',
        ], 200);
    }
}
