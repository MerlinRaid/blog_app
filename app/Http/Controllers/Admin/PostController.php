<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Post::with(['author', 'category', 'tags']) 
            ->when($request->status, fn($qq)=>$qq
            ->where('status', $request->status))
            ->latest('created_at')
            ->paginate(5)->withQueryString();
        return view('admin.posts.index', ['posts' => $q]);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.posts.create',[
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:posts,slug'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft','review', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'tag' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
    ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']).'-'.Str::random(5);
        $data['user_id'] = Auth::id();

        //Staatuse muutmine (robustne)
        if($data['status'] === 'published'){
            $data['published_at'] = $data['published_at'] ?? now();
        } else {
            $data['published_at'] = null;
        }
        
        //Pildi üleslaadimine
        if($request->hasFile('featured_image')){
            //Salvestame public kettale kausta posts
            $path = $request->file('featured_image')->store('posts', 'public');
            $data['featured_image'] = $path; //näiteks posts/image.jpg
        }

        $post = Post::create($data);
        $post->tags()->sync($request->input('tag_ids', []));

        return redirect()->route('admin.posts.index', $post)
            ->with('status', 'Postitus lisatud!');
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
        return view('admin.posts.edit', [
            'post' => $post ->load('tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('posts','slug')->ignore($post->id)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft','review', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'tag' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
        ]);

        //Staatuse muutmine (robustne)
        if($data['status'] === 'published'){
            $data['published_at'] = $data['published_at'] ?? now();
        } else {
            $data['published_at'] = null;
        }

        $tagIds = $request->input('tag_ids', []);
        $removeImage = $request->boolean('remove_image');

        //Ära pane neid $post-> update() sisse
        unset($data['featured_image'], $data['remove_image'], $data['tag_ids']);

        //Põhiandmete uuendamine
        $post->update($data);

        //Pildi käitlemine
        if($removeImage && $post->featured_image){
            Storage::disk('public')->delete($post->featured_image); //kustutame faili
            $post->featured_image = null; //eemaldame postituse kirjest 
        }
        if($request->hasFile('featured_image')){
            if($post->featured_image){
                Storage::disk('public')->delete($post->featured_image);
            }
            $newPath = $request->file('featured_image')->store('posts', 'public');
            $post->featured_image = $newPath;
        } 

        $post->save(); //salveastamine

        //Sildid
        $post->tags()->sync($tagIds);

        return redirect()->route('admin.posts.index', $post)
            ->with('status', 'Postitus uuendatud!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if($post->featured_image){
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->comments()->delete();
        $post->delete();
        return redirect()->route('admin.posts.index')
            ->with('status', 'Postitus kustutatud!');
    }

    //Kiirtegevused avalda ja peida
    public function publish(Post $post)
    {
        //Kohandatud meetod => autoriseeri käsitsi
        $this->authorize('publish', $post);

        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('status', 'Postitus avaldatud!');
    }

    public function unpublish(Post $post)
    {
        //Kohandatud meetod => autoriseeri käsitsi
        $this->authorize('publish', $post);

        $post->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return back()->with('status', 'Eemaldatud avaldatud postituste hulgast!');
    }
}
