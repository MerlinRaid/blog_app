<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
     public function __construct()
    {
        $this->authorizeResource(Tag::class, 'tag');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::orderBy('name')->paginate(5)->withQueryString();
        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:80'],
            'slug' => ['nullable','string','max:120','unique:tags,slug'],
        ]);

         $data['slug'] =  Str::slug($data['name']);
        Tag::create($data);

        return redirect()->route('admin.tags.index')
            ->with('status', 'Silt on loodud.');            
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'slug' => ['nullable','string','max:160',Rule::unique('tags','slug')->ignore($tag->id) ],
            'description' => ['nullable','string'],
        ]);

         $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $tag->update($data);

        return redirect()->route('admin.tags.index')
            ->with('status', 'Silt on uuendatud.');     
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete(); 
         return redirect()->route('admin.tags.index')
            ->with('status', 'Silt on kustutatud.' );    
    }
}
