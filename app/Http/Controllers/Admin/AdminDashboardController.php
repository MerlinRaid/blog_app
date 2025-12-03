<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $needsAction = Post::with('author')
            ->needsAction()
            ->latest('updated_at')
            ->limit(10)
            ->get();

        $scheduled = Post::with('author')
            ->scheduled()
            ->orderBy('published_at')
            ->limit(10)
            ->get();
        
        $recent = Post::with('author')
            ->published()
            ->latest()
            ->limit(10)
            ->get();
        
        $pendingComments = Comment::with('author', 'post')
            ->where('status','pending')
            ->latest()
            ->limit(10)
            ->get();

    // Arhiveeritud postitused
    $archived = Post::with('author')
        ->where('status', 'archived')
        ->latest('updated_at')
        ->limit(10)
        ->get();

    // Orvuks jäänud kommentaarid (ilma seotud postituseta)
    $orphanedComments = Comment::with('author')
        ->doesntHave('post')
        ->latest()
        ->limit(10)
        ->get();

    // Prügikast (soft-deleted)
    $trashed = Post::with('author')
        ->onlyTrashed()
        ->latest('deleted_at')
        ->limit(10)
        ->get();
        
          return view('admin.dashboard', compact(
        'needsAction',
        'scheduled',
        'recent',
        'pendingComments',
        'archived',
        'orphanedComments',
        'trashed'
    ));
    }
}

