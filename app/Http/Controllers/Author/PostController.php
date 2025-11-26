<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
   public function __construct() {
        $this->authorizeResource(Post::class, 'post');
    }
    public function index(Request $request)
    {
      //Ainult autori enda postitused
        $posts = Post::with(['category', 'tags']) ->where('user_id',  Auth::id()) ->latest()->paginate(15) ->withQueryString();

        return view('author.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('author.posts.create', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),

        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:220', 'unique:posts,slug'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg, webp', 'max:4096'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']).'-'.Str::random(5);
        $data['user_id'] = Auth::id();
        $data['status'] = 'draft';
        $data['published_at'] = null;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $data['featured_image'] = $path;
        }

        $post = Post::create($data);
        $post->tags()->sync($request->input('tag_ids', []));

        return redirect()->route('author.posts.index')->with('status', 'Postitus loodud.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $user = Auth::user();
        abort_unless((int) $post->user_id || $user->hasAnyRole(['Admin']), 403);

        return view('author.posts.edit', [
            'post' => $post->load('tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('posts', 'slug')->ignore($post->id)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg, webp', 'max:4096'],
        ]);

        //Autor ei muuda staatust/avaldamise aega, kui see on juba seatud
        unset($data['status'], $data['published_at'], $data['user_id']);

        $tagIds = $request->input('tag_ids', []);
        $remove = $request->boolean('remove_featured_image');

        unset($data['featured_image'], $data['remove_image'], $data['tag_ids']);

        $post->update($data);

        //Kas eemaldada olemasolev pilt
        if ($remove && $post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
            $post->featured_image = null;
        }

        //Kas lisada uus pilt
        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image); 
            }
            $newPath = $request->file('featured_image')->store('posts', 'public');
            $post->featured_image = $newPath;
        }
        $post->save();
        $post->tags()->sync($tagIds);

        return redirect()->route('author.posts.edit', $post)->with('status', 'Postitus muudetud.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //Policy kontrollib kustutamist (soft delete). Siia jõudes on juba lubatud
        $post->delete();
        return redirect()->route('author.posts.index')->with('status', 'Postitus kustutatud.');
    }
}
