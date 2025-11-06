<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentModerationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }
    
    public function index(Request $request)
    {
     $comments = Comment::with([ 'author','post:id,title,slug'])
     ->when($request->status, fn($q)=>$q->where('status',$request->status))
     ->latest('created_at')
    ->paginate(15)->withQueryString();

    return view('admin.comments.index', compact('comments'));        
    }

    public function updateStatus(Request $request, Comment $comment)
    {
        //Modderaatoril peba olemas õigus uuendada
        $this->authorize('update', $comment);

        $data = $request->validate ([
            'status' => ['required', Rule::in(['pending', 'approved', 'hidden', 'spam'])],
        ]);

        $comment->update(['status'=> $data['status']]);
        return back()->with('status', 'Staatus uuendatud: '.$data['status']);
    }

    public function destroy(Comment $comment)
    {   
        //Ainult adminnil on lubatud kustutada
        $this->authorize('delete', $comment);

        $comment->delete();
        return back()->with('status', 'Kommentaar kustutatud.');
    }
}
