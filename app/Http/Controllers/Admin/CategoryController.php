<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $categories = Category::orderBy('name')->paginate(5)->withQueryString();
      return view('admin.categories.index', compact('categories'));  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'slug' => ['nullable','string','max:160','unique:categories,slug'],
            'description' => ['nullable','string'],
        ]);

         $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategooria on loodud.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'slug' => ['nullable','string','max:160',Rule::unique('categories','slug')->ignore($category->id)],
            'description' => ['nullable','string'],
        ]);

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategooria on uuendatud:' .$data['name'] );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategooria on kustutatud: ' . $category->name);   
    }
}
