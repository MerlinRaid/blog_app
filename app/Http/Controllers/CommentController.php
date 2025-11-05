<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $request->validate([
            'body' => ['required','string','min:5'],
        ]);
        $post = Post::where('slug', $slug)->firstOrFail();

        Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'body' => $request->input('body'),
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('blog.show', $post->slug)
            ->with('status', 'Aitäh! Sinu kommentaar on esitatud ja ootab heakskiitu.');
    }
}
